<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/11/14
 * Time: 12:11 PM
 */

namespace Localization\API;


use Application\API\App;
use Localization\Model\TranslationTable;
use System\API\BaseAPI;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class Translation extends BaseAPI
{
    const CACHE_KEY = "translation_tables";
    private $config = null;
    /**
     * @var TranslationTable
     */
    private $table = null;
    private $tables_cache = null;


    public function translateFields($entityType)
    {
        return $this->translate(null, $entityType);
    }

    /**
     * @param $mainSelect Select
     * @param $entityType
     * @param bool $lang
     * @return bool
     */
    public function translate($mainSelect, $entityType, $lang = false)
    {
        if (!$lang)
            $lang = SYSTEM_LANG;

        $defaultLang = getSM('language_table')->getDefault();
        if ($lang == $defaultLang)
            return false;

        $this->getCache();

        $mainTable = $mainPk = $tTable = null;

        $mainConfig = $this->getConfig();

        $fields = array();
        if (!is_array($entityType)) {
            if (!isset($mainConfig[$entityType]))
                return array();

            if (!isset($this->tables_cache[$entityType]))
                $this->checkColumns($entityType, $mainConfig[$entityType]);

            $tTable = 'tbl_translation_' . strtolower($entityType);
            $config = $mainConfig[$entityType];
            $mainTable = $config['table'];
            $mainPk = $config['pk'];

            foreach ($config['fields'] as $col => $conf) {
                $fields[$col] = $col;
            }
        } elseif (is_array($entityType)) {

            foreach ($entityType as $et)
                if (!isset($mainConfig[$et]))
                    return array();

            $config = $mainConfig[$entityType[0]];
            $sharedEntityType = $config['entityType'];
            $mainTable = $config['table'];
            $mainPk = $config['pk'];
            $tTable = 'tbl_translation_' . strtolower($sharedEntityType);

            foreach ($entityType as $et) {
                if (!isset($this->tables_cache[$et]))
                    $this->checkColumns($et, $mainConfig[$et]);
                foreach ($mainConfig[$et]['fields'] as $col => $conf)
                    $fields[$col] = $col;
            }
        }

        $columns = array();
        foreach ($fields as $key => $value) {
            $columns[$key] = new Expression('IFNULL(' . $tTable . '.' . $key . ', ' . $mainTable . '.' . $key . ')');
        }

        $expr = $tTable . '.entityId=' . $mainTable . '.' . $mainPk;
        $expr .= ' AND ' . $tTable . '.lang="' . $lang . '"';
        $join = new Expression($expr);

        if ($mainSelect)
            $mainSelect->join($tTable, $join, $columns, 'LEFT');
        else
            return array($tTable, $join, $columns, 'LEFT');

        return $tTable;
    }

    public function getConfig($entityType = null)
    {
        if (is_null($this->config)) {

            $cacheKey = 'localizable_merged_config';
            if (!IS_DEVELOPMENT_SERVER)
                $this->config = getCacheItem($cacheKey);

            if (!$this->config) {
                $this->config = array();
                $modules = getSM('ModuleManager')->getModules();
                foreach ($modules as $m) {
                    $f = ROOT . '/module/' . $m . '/config/localizable.config.php';
                    if (file_exists($f))
                        $this->config = array_merge_recursive($this->config, include $f);
                }

                $this->getEventManager()->trigger('translation.dynamicEntityTypes', $this);

                if (!IS_DEVELOPMENT_SERVER)
                    setCacheItem($cacheKey, $this->config);
            }
        }
        if ($entityType) {
            if (isset($this->config[$entityType]))
                return $this->config[$entityType];
            else
                return null;
        }
        return $this->config;
    }

    public function addToConfig($entityType, $config)
    {
        $this->config[$entityType] = $config;
    }

    private function checkTable($entityType, $config)
    {
        $sharedEntityType = $entityType;
        if (isset($config['entityType']))
            $sharedEntityType = $config['entityType'];

        $clean_table_name = 'tbl_translation_' . strtolower($sharedEntityType);
        $tables = $this->getCache();
        if ($tables && isset($tables[$sharedEntityType]))
            return $clean_table_name;

        $adapter = App::getDbAdapter();

        //region Table
        $table_name = $adapter->getPlatform()->quoteIdentifier($clean_table_name);
        $q = "CREATE TABLE IF NOT EXISTS {$table_name} (
                    `entityId` int(11) NOT NULL,
                    `lang` varchar(4) NOT NULL,
                    UNIQUE KEY `entityId` (`entityId`,`lang`)
                  ) ENGINE=InnoDB
                  CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";

        $adapter->query($q)->execute();
        //endregion

        $tables[$entityType] = array('table' => true);
        $this->setCache($tables);

        return $clean_table_name;
    }

    public function checkColumns($entityType, $config)
    {
        $clean_table_name = $this->checkTable($entityType, $config);
        $tables = $this->getCache();
        $columns = array_keys($config['fields']);

        //region compare columns list in the cache to the fields list in config file
        if (isset($tables[$entityType]['columns'])) {
            $diff = array_diff($columns, $tables[$entityType]['columns']);
            // columns in the cache are the same as config files
            if (count($diff) == 0)
                return $clean_table_name;
        }
        //endregion

        $adapter = App::getDbAdapter();
        $table_name = $adapter->getPlatform()->quoteIdentifier($clean_table_name);
        //region Get Columns list from table
        $q = "SHOW COLUMNS FROM {$table_name}";
        $result = $adapter->query($q)->execute();
        $old_columns = array();
        foreach ($result as $column) {
            if (!in_array($column['Field'], array('lang', 'entityId')))
                $old_columns[] = $column['Field'];
        }
        //endregion

        //region Drop old(removed) columns
        foreach ($old_columns as $old) {
            if (!in_array($old, $columns)) {
                $old = $adapter->getPlatform()->quoteIdentifier($old);
                $q = "ALTER TABLE {$table_name} DROP {$old}";
                $adapter->query($q)->execute();
            }
        }
        //endregion

        //region Add new Columns
        foreach ($columns as $new) {
            if (!in_array($new, $old_columns)) {
                $col = $adapter->getPlatform()->quoteIdentifier($new);
                $type = $config['fields'][$new]['column_type'];
                $specs = '';
                if (isset($config['fields'][$new]['collate']))
                    $specs = 'CHARACTER SET utf8 COLLATE ' . $config['fields'][$new]['collate'];

                $q = "ALTER TABLE {$table_name} ADD COLUMN {$col} {$type} {$specs} DEFAULT NULL";
                $adapter->query($q)->execute();
            }
        }
        //endregion

        $tables[$entityType]['columns'] = $columns;
        $this->setCache($tables);

        return $clean_table_name;
    }

    /**
     * @param $table
     * @return TranslationTable
     */
    public function getTable($table)
    {
        if (is_null($this->table))
            $this->table = new TranslationTable($table, App::getDbAdapter());

        return $this->table;
    }

    private function getCache()
    {
        if (is_null($this->tables_cache))
            $this->tables_cache = getCache(true)->getItem(self::CACHE_KEY);

        return $this->tables_cache;
    }

    private function setCache($cache)
    {
        $this->tables_cache = $cache;
        getCache(true)->setItem(self::CACHE_KEY, $this->tables_cache);
    }

    /**
     * @param $mainSelect Select
     * @param $entityType
     * @param $identityField
     */
    public function filter($mainSelect, $entityType, $identityField)
    {
        $select = new Select();
        $select
            ->from('tbl_language_content')
            ->columns(array(new Expression('DISTINCT(entityId) as entityId')))
            ->where(array(
                'tbl_language_content.langSign' => SYSTEM_LANG,
                'tbl_language_content.entityType' => $entityType
            ));

        $select2 = new Select();
        $select2
            ->from('tbl_language_content')
            ->columns(array(new Expression('DISTINCT(entityId) as entityId')))
            ->where(array(
                'tbl_language_content.entityType' => $entityType
            ));

        $where = new Where();

        $where->addPredicate(new In($identityField, $select));
        $where->addPredicate(new NotIn($identityField, $select2), 'OR');

        $mainSelect->where->addPredicate($where);
    }
} 