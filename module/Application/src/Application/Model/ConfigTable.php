<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/5/12
 * Time: 10:25 AM
 */
namespace Application\Model;

use Application\API\App;
use Application\Model\Config;
use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class ConfigTable extends BaseTableGateway
{
    protected $table = 'tbl_config';
    protected $model = 'Application\Model\Config';
    protected $caches = null;
    private $allConfigs = null;

    public function fetchAll()
    {
        if (is_null($this->allConfigs)) {
//            $this->allConfigs = App::getGlobalSystemCache('all_configs');
            if (!$this->allConfigs) {
                $all = $this->select();
                $configs = array();
                foreach ($all as $con) {
                    $configs[$con->varName] = $con;
                }
//                App::setGlobalSystemCache('all_configs', $configs);
                $this->allConfigs = $configs;
            }
        }
        return $this->allConfigs;
    }

    public function getByVarName($name)
    {
        if ($config = getCache(true)->getItem('config_' . $name))
        return $config;

        $result = $this->select(array('varName' => $name));
        if ($result && $result->count()) {
            $result = $result->current();
            if (!empty($result->varValue) && !is_array($result->varValue))
                $result->varValue = @unserialize($result->varValue);
            $config = $result;
        } else {
            $config = new Config();
            $config->setVarName($name);
            $config->setVarValue(array());
        }

        getCache(true)->setItem('config_' . $name, $config);
//        $configs = $this->fetchAll();
//        if (isset($configs[$name])) {
//            if (!empty($configs[$name]->varValue) && !is_array($configs[$name]->varValue))
//                $configs[$name]->varValue = @unserialize($configs[$name]->varValue);
//            return $configs[$name];
//        }
//        $config = new Config();
//        $config->setVarName($name);
//        $config->setVarValue(array());
        return $config;
    }

    public function save(Config $config)
    {
        $value = $config->getVarValue();
        unset($value['buttons']);
        $config->setVarValue(serialize($value));
        parent::save($config);
//        App::setGlobalSystemCache('all_configs', null);
//        $this->allConfigs = null;
        getCache(true)->removeItem('config_' . $config->varName);
    }
}
