<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/25/13
 * Time: 11:12 AM
 */

namespace System\DB;

use \Zend\Db\Adapter\Adapter;

class BaseInstall
{
    /**
     * @var Adapter
     */
    protected $db;
    protected $installScript = 'install.sql';
    protected $insertScript = 'insert.sql';
    protected $params = array();
    protected $module = null;

    public function __construct(Adapter $db, $module, array $params = array())
    {
        $this->db = $db;
        $this->params = $params;
        $this->module = $module;
    }

    public function createTable()
    {
        $sqlFile = ROOT . '/module/' . $this->module . '/' . $this->installScript;

        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $this->db->query($sql)->execute();
        }
    }

    public function initialize()
    {
        $sqlFile = ROOT . '/module/' . $this->module . '/' . $this->insertScript;

        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $this->db->query($sql)->execute();
        }
    }

    public function update($installedVersion, $newVersion)
    {
        $errors = array();
        $functions = get_class_methods(get_called_class());
        $updateFunctions = array();
        foreach ($functions as $name) {
            if (strpos($name, 'update_') > -1) {
                $updateFunctions[] = $name;
            }
        }
        if (count($updateFunctions)) {
            foreach ($updateFunctions as $update) {
                $version = explode('_', $update);
                $version = $version[1];

                if ($this->__versionCompare($installedVersion, $version) == -1) {
                    if ($this->__versionCompare($newVersion, $version) != -1) {
                        try {
                            $this->{$update}();
                        } catch (\Exception $e) {
                            $errors[] = get_called_class() . "<br/>" . $update . '<br/>' . $e->getMessage();
                            if ($e->getPrevious()) {
                                $errors[] = $e->getPrevious()->getMessage();
                            }
                        }
                    }
                    continue;
                }
            }
        }
        return $errors;
    }

    protected function multiInsert($table, array $data)
    {
        if (count($data)) {
            $columns = (array)current($data);
            $columns = array_keys($columns);
            $columnsCount = count($columns);
            $platform = $this->db->platform;
            array_filter($columns, function ($index, &$item) use ($platform) {
                $item = $platform->quoteIdentifier($item);
            });
            $columns = "(" . implode(',', $columns) . ")";

            $placeholder = array_fill(0, $columnsCount, '?');
            $placeholder = "(" . implode(',', $placeholder) . ")";
            $placeholder = implode(',', array_fill(0, count($data), $placeholder));

            $values = array();
            foreach ($data as $row) {
                foreach ($row as $key => $value) {
                    $values[] = $value;
                }
            }


            $table = $this->db->platform->quoteIdentifier($table);
            $q = "INSERT INTO $table $columns VALUES $placeholder";
            $this->db->query($q)->execute($values);
        }
    }

    private function __versionCompare($v1, $v2)
    {
        $v1 = $this->__versionCleanup($v1);
        $v2 = $this->__versionCleanup($v2);

        return version_compare($v1, $v2);
    }

    private function __versionCleanup($version)
    {
        $version = str_replace('.', '', $version);
        $vLen = strlen($version);
        if ($vLen < 5)
            $version = $version . str_repeat('0', 5 - $vLen);
        return $version;
    }
}