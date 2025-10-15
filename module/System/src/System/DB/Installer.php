<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/25/13
 * Time: 11:45 AM
 */

namespace System\DB;

use Zend\Db\Adapter\Adapter;

class Installer
{
    private $modules = array();
    public $messages = array();
    /**
     * @var Adapter
     */
    private $db;

    public function __construct(Adapter $db, array $modules)
    {
        foreach ($modules as $key => $value) {
            $params = array();
            if (is_int($key))
                $class = ucfirst($value);
            else {
                $class = ucfirst($key);
                $params = $value;
            }
            $file = ROOT . '/module/' . $class . '/src/' . $class . '/Install.php';
            if (file_exists($file)) {
                include_once $file;
                $class_name = $class . '\\Install';
                $class_object = new $class_name($db, $class, $params);
            } else {
                $file = ROOT . '/module/System/src/System/DB/BaseInstall.php';
                include_once $file;
                $class_object = new BaseInstall($db, $class, $params);
            }
            $this->modules[$class] = $class_object;
        }
        $this->db = $db;
    }

    public function install()
    {
        $this->createTables();
        $this->initialize();

        $placeHolder = implode(',', array_fill(0, count($this->modules), "(?,?)"));
        $q = "insert into `tbl_modules` (`name`,`dbVersion`) values " . $placeHolder;
        $values = array();
        foreach ($this->modules as $name => $class) {
            $config = parse_ini_file(ROOT . '/module/' . $name . '/module.ini');
            $version = '1.0';
            if (isset($config['version']))
                $version = $config['version'];
            $values[] = $name;
            $values[] = $version;
        }
        $this->db->query($q, $values);
    }

    private function createTables()
    {
        /* @var $class BaseInstall */
        foreach ($this->modules as $name => $class) {
            $class->createTable();
        }
    }

    private function initialize()
    {
        /* @var $class BaseInstall */
        foreach ($this->modules as $name => $class) {
            $class->initialize();
        }
    }

    private function update(){}
} 