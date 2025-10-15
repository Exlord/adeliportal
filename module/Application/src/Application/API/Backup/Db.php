<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/9/13
 * Time: 1:19 PM
 */

namespace Application\API\Backup;


use Application\Model\DbBackupTable;
use System\API\BaseAPI;
use System\IO\Directory;
use System\IO\File;
use Zend\Db\Adapter\Adapter;
use Zend\Filter\Compress;
use Zend\Filter\Decompress;
use Zend\Stdlib\InitializableInterface;

class Db extends BaseAPI
{
    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    private $adapter;
    private $ignoredTables = array('tbl_db_backup', 'tbl_session');
    private $dir;
    private $tempDir;
    private $tablesTempDir;
    private $dataTempDir;
    private $lockFile;
    private $dbName;
    private $totalSizeFile;

    private $tempDb = "temp_table";
    private $createTempDbQuery = "CREATE TABLE `temp_table` (`id` INT(11)) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    private $dropTempDbQuery = "DROP TABLE `temp_table`;";

    public $error;
    public $fileSize;

    public static $autoInterval = array(
        '0' => 'Disabled',
        '+1 hour' => '1 Hour',
        '+6 hours' => '6 Hours',
        '+12 hours' => '12 Hours',
        '+24 hours' => '24 Hours',
        '+3 days' => 'every 3 days',
        '+1 week' => 'every week',
        '+2 weeks' => 'twice a month',
        '+1 month' => 'every month',
    );

    public static $autoCleanupDistance = array(
        '-1' => '-- Select --',
        '0' => 'Disabled',
        '-1 week' => '1 week',
        '-2 week' => '2 week',
        '-1 month' => '1 month',
        '-3 month' => '3 month',
        '-6 month' => '6 month',
        '-1 year' => '1 year',
        '-2 year' => '2 year',
        '-5 year' => '5 year',
        '-10 year' => '10 year',
    );

    //region Public Methods
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->dbName = $this->adapter->getDriver()->getConnection()->getConnectionParameters();
        $this->dbName = $this->dbName['database'];
        $this->dir = str_replace('\\', '/', PRIVATE_FILE . '/backup/db');
        $this->tempDir = $this->dir . '/tmp';
        $this->tablesTempDir = $this->tempDir . '/' . $this->dbName . '/tables';
        $this->dataTempDir = $this->tempDir . '/' . $this->dbName . '/data';
        $this->lockFile = $this->dir . '/lock';
        $this->totalSizeFile = $this->dir . '/TotalSize';
    }

    public function delete($file, $size = 0)
    {
        $file = $this->dir . '/' . $file;
        if (is_file($file)) {
            if (!$size)
                $size = File::getSize($file);
            @unlink($file);
        }
        $this->addToTotalSize($size * -1);
    }

    public function backup($tables = array())
    {
        if ($this->isLocked()) {
            $this->error = t('Another backup instance is running and the folder is locked.');
            return false;
        }
        $this->mkDirs();
        $this->lock();
        $tables = $this->getTables($tables);
        $this->makeTablesSql($tables);
        $counts = $this->getDataCounts($tables);
        $this->makeDataFiles($counts);
        $file = $this->compressFiles();
        $this->unlock();
        return $file;
    }

    public function restore($backup)
    {
        if ($this->isLocked()) {
            $this->error = t('Another backup instance is running and the folder is locked.');
            return false;
        }

        $result = $this->checkPermission();
        if (!$result) {
            $this->unlock();
            return false;
        }

        $this->mkDirs();
        $this->lock();
        $file = $this->dir . '/' . $backup;
        $decompressed = $this->decompressFiles($file);
        if (!$decompressed) {
            $this->unlock();
            return false;
        }

        $result = $this->__restore();
        if (!$result) {
            $this->unlock();
            return false;
        }

        $this->unlock();
        return true;
    }

    public static function getConfig()
    {
        $config = getConfig('system_config')->varValue;
        $myConfig = array();

        //newDbBackupInterval
        if (isset($config['newDbBackupInterval']))
            $myConfig['newDbBackupInterval'] = $config['newDbBackupInterval'];
        else
            $myConfig['newDbBackupInterval'] = null;

        //dbBackupCleanupInterval
        if (isset($config['dbBackupCleanupInterval']))
            $myConfig['dbBackupCleanupInterval'] = $config['dbBackupCleanupInterval'];
        else
            $myConfig['dbBackupCleanupInterval'] = '+6 month';

        //dbBackupMaxCount
        $dbBackupMaxCount = 100;
        if (isset($config['dbBackupMaxCount'])) {
            if ($config['dbBackupMaxCount'] == '0')
                $dbBackupMaxCount = false;
            elseif ((int)$config['dbBackupMaxCount'])
                $dbBackupMaxCount = (int)$config['dbBackupMaxCount'];
        }
        $myConfig['dbBackupMaxCount'] = $dbBackupMaxCount;

        //dbBackupMaxSize
        $dbBackupMaxSize = '1073741824';
        if (isset($config['dbBackupMaxSize'])) {
            if ($config['dbBackupMaxSize'] == '0')
                $dbBackupMaxSize = false;
            elseif (strlen($config['dbBackupMaxSize']) >= 3) {
                $type = substr($config['dbBackupMaxSize'], strlen($config['dbBackupMaxSize']) - 2);
                $types = array('B' => 1, 'KB' => 1024, 'MB' => 1048576, 'GB' => 1073741824, 'TB' => 1099511627776);
                if (isset($types[$type])) {
                    $value = (int)substr($config['dbBackupMaxSize'], 0, strlen($config['dbBackupMaxSize']) - 2);
                    $dbBackupMaxSize = $value * $types[$type];
                }
            }
        }
        $myConfig['dbBackupMaxSize'] = $dbBackupMaxSize;

        return $myConfig;
    }

    /**
     * unlock tmp folder
     */
    public function unlock()
    {
        @Directory::clear($this->tempDir, true);
        @unlink($this->lockFile);
    }

    /**
     * @return bool check if tmp folder is locked
     */
    public function isLocked()
    {
        return file_exists($this->lockFile);
    }

    public function getTotalSize()
    {
        if (!file_exists($this->totalSizeFile)) {
            $tables = getSM('db_backup_table')->select();
            $totalSize = 0;
            if ($tables && $tables->count()) {
                foreach ($tables as $t) {
                    $totalSize += $t->size;
                }
            }
            file_put_contents($this->totalSizeFile, $totalSize);
            return $totalSize;
        }
        return file_get_contents($this->totalSizeFile);
    }

    public function addToTotalSize($size)
    {
        $total = $this->getTotalSize();
        $total += $size;
        file_put_contents($this->totalSizeFile, $total);
        return $total;
    }
    //endregion

    //region Private Methods
    /**
     * Check if user has permission to create and drop tables
     */
    private function checkPermission()
    {
        try {
            $this->adapter->query($this->createTempDbQuery)->execute();
            $this->adapter->query($this->dropTempDbQuery)->execute();
            return true;
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            if ($error == 'Statement could not be executed') {
                $error = $ex->getPrevious()->getMessage();
                if (strpos($error, "CREATE command denied to user") !== false) {
                    $this->error = t("You don't have permission to create database tables");
                    return false;
                } elseif (strpos($error, "DROP command denied to user") !== false) {
                    $this->error = t("You don't have permission to drop database tables");
                    return false;
                }
            }
            $this->error = ___exception_trace($ex);
            return false;
        }
    }

    private function prefixOldTables($old_tables)
    {
        try {
            foreach ($old_tables as $tbl) {
                $tbl_name = $this->adapter->platform->quoteIdentifier($tbl);
                $old_tbl_name = $this->adapter->platform->quoteIdentifier("old__" . $tbl);
                $q = "RENAME TABLE " . $tbl_name . " TO " . $old_tbl_name . ";";
                $this->adapter->query($q)->execute();
            }
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            if ($error == 'Statement could not be executed') {
                $error = $ex->getPrevious()->getMessage();
                if (strpos($error, "ALTER command denied to user") !== false) {
                    $this->error = t("You don't have permission to alter database tables");
                    return false;
                }
            }
            $this->error = ___exception_trace($ex);
            return false;
        }
    }

    private function __restore()
    {
        $tables = Directory::getFiles($this->tablesTempDir, true, array('table'));
        if (is_array($tables) && count($tables)) {
            $old_tables = $this->getTables($tables);
            $this->prefixOldTables($old_tables);

            //create new tables
            foreach ($tables as $tbl) {
                $q = file_get_contents($tbl) . "\n";
                $this->adapter->query($q)->execute();
            }

            //insert data
            $data_files = Directory::getFiles($this->dataTempDir, true, array('data'));
            foreach ($data_files as $data) {
                $this->insertData($data);
            }
            //remove old tables
            foreach ($old_tables as $tbl) {
                $q = "DROP TABLE IF EXISTS " . $this->adapter->platform->quoteIdentifier("old__" . $tbl) . ";";
                $this->adapter->query($q)->execute();
            }
        } else {
            $this->error = t('Unable to find any table sql file in temp folder');
            return false;
        }
        return true;
    }

    private function insertData($data)
    {
        $data = file_get_contents($data);
        $data = json_decode($data, 'true');
        $table = $data['table'];
        $data = $data['data'];
        if (is_array($data) && count($data)) {
            $columns = array_keys(current($data));
            $platform = $this->adapter->platform;
            array_walk($columns, function (&$item, $index) use ($platform) {
                $item = $platform->quoteIdentifier($item);
            });

            $placeholder = "(" . implode(',', array_fill(0, count($columns), '?')) . ")";
            $placeholder = implode(',', array_fill(0, count($data), $placeholder));

            $query = "INSERT INTO " .
                $this->adapter->platform->quoteIdentifier($table) .
                " (" . implode(',', $columns) . ") VALUES " . $placeholder;

            $rows = array();
            foreach ($data as $row) {
                foreach ($row as $value) {
                    $rows[] = $value;
                }
            }
            $this->adapter->query($query, $rows);
        }
    }

    private function mkDirs()
    {
        if (!is_dir($this->tablesTempDir))
            mkdir($this->tablesTempDir, 0755, true);
        if (!is_dir($this->dataTempDir))
            mkdir($this->dataTempDir, 0755, true);

        Directory::clear($this->tablesTempDir);
        Directory::clear($this->dataTempDir);
    }

    /**
     * lock the tmp folder so only one back up instance can be run at the same time
     */
    private function lock()
    {
        file_put_contents($this->lockFile, 'This folder is locked');
    }

    /**
     * Gets a list of tables from the currently selected db
     * @return array
     */
    private function getTables()
    {
        $tables = array();
        $q = 'SHOW TABLES';
        $statement = $this->adapter->query($q);
        $result = $statement->execute();
        foreach ($result as $row) {
            $tables[] = array_shift($row);
        }
        foreach ($this->ignoredTables as $ign) {
            if (($key = array_search($ign, $tables)) !== false) {
                unset($tables[$key]);
            }
        }
        return ($tables);
    }

    /**
     * Creates sql string for the tables
     * @param $tables
     */
    private function makeTablesSql($tables)
    {
        foreach ($tables as $tbl) {
            $q = 'SHOW CREATE TABLE ' . $this->adapter->platform->quoteIdentifier($tbl);
            $statement = $this->adapter->query($q);
            $result = $statement->execute()->current();
            $fileName = $this->tablesTempDir . '/' . $tbl . '.table';
            $string = "DROP TABLE IF EXISTS " . $this->adapter->platform->quoteIdentifier($tbl) . ";\n";
            file_put_contents($fileName, $string . $result['Create Table']);
        }
    }

    private function getDataCounts($tables)
    {
        $counts = array();
        foreach ($tables as $tbl) {
            $q = 'SELECT COUNT(*) as count FROM ' . $this->adapter->platform->quoteIdentifier($tbl);
            $statement = $this->adapter->query($q);
            $result = $statement->execute()->current();
            $counts[$tbl] = $result['count'];
        }
        return ($counts);
    }

    private function makeDataFiles($tables)
    {
        $max = 10000;
        foreach ($tables as $tbl => $count) {
            $index = 1;
            $offset = 0;
            $limit = 10000;
            $rowCount = $count;
            do {
                $this->__makeDataFile($tbl, $offset, $limit, $index);
                $offset = $limit;
                $limit += $max;
                $rowCount -= $max;
                $index++;
            } while ($rowCount > $limit);
        }
    }

    /*private function __makeDataFile($table, $offset, $limit, $index = 1)
    {
        $file = str_replace('\\', '/', $this->dataTempDir . '/' . $table . '_' . $index . '.data');
        $q = sprintf("SELECT * INTO OUTFILE '%s'
                  FIELDS TERMINATED BY '\\t' ENCLOSED BY '\"' ESCAPED BY '\\\\'
                  LINES TERMINATED BY '\\n' STARTING BY ''
                  FROM %s LIMIT %s,%s;", $file, $this->adapter->platform->quoteIdentifier($table), $offset, $limit);
        $statement = $this->adapter->query($q);
        $statement->execute();
    }*/
    private function __makeDataFile($table, $offset, $limit, $index = 1)
    {
        $file = str_replace('\\', '/', $this->dataTempDir . '/' . $table . '_' . $index . '.data');
        $q = sprintf("SELECT * FROM %s LIMIT %s,%s;", $this->adapter->platform->quoteIdentifier($table), $offset, $limit);
        $statement = $this->adapter->query($q);
        $result = $statement->execute();

        /* @var $res \PDOStatement */
        $res = $result->getResource();
        $data = json_encode(array(
            'table' => $table,
            'data' => $res->fetchAll(\PDO::FETCH_ASSOC)
        ));
        file_put_contents($file, $data);
    }

    private function compressFiles()
    {
        //Gz ,Zip
        $ext = '.archive';
        $fileName = 'db-' . time() . $ext;
        $path = $this->dir . '/' . $fileName;
        $filter = new Compress(array(
            'adapter' => 'Zip',
            'options' => array(
                'archive' => $path,
            ),
        ));
        $file = $filter->filter($this->tempDir . '/' . $this->dbName);
        @Directory::clear($this->tempDir, true);

        $this->fileSize = File::getSize($file);
        $this->addToTotalSize($this->fileSize);
        return $fileName;
    }

    private function decompressFiles($fileName)
    {
        $filter = new Decompress(array(
            'adapter' => 'Zip',
            'options' => array(
                'target' => $this->tempDir,
                'archive' => $fileName,
            )
        ));
        $result = $filter->filter($fileName); //result is the temp dir path

        if ($this->tempDir . '/' != str_replace('\\', '/', $result)) {
            $this->error = t('Unable to decompress the archive!.' . $result);;
            return false;
        }
        return true;
    }
    //endregion
}