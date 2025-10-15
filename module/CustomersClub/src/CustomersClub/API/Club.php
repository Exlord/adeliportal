<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/17/14
 * Time: 11:05 AM
 */

namespace CustomersClub\API;


use System\API\BaseAPI;
use System\IO\Directory;
use Zend\ModuleManager\ModuleManager;

class Club extends BaseAPI
{
    private $_baseConfigs = null;
    public $records = array();

    public function loadBaseConfig()
    {
        if (!is_null($this->_baseConfigs))
            return $this->_baseConfigs;

        $cacheKey = "customers_club_modules_config";
        if ($this->_baseConfigs = getCacheItem($cacheKey)) {
            return $this->_baseConfigs;
        }

        $list = array();
        /* @var $moduleManager ModuleManager */
        $moduleManager = getSM('ModuleManager');
        $modules = $moduleManager->getModules();

        foreach ($modules as $moduleName) {
            $configFile = ROOT . '/module/' . $moduleName . '/config/cc.points.config.php';
            if (file_exists($configFile))
                $list = array_merge_recursive($list, include $configFile);
        }
        setCacheItem($cacheKey, $list);
        $this->_baseConfigs = $list;
        return $list;
    }

    public function getRecords($userId)
    {
        $this->getEventManager()->trigger('CC.CustomerRecords.Load', $this, array('userId' => $userId));
        return $this->records;
    }

    public function loadConfigs($moduleFieldset)
    {
        $this->getEventManager()->trigger(
            'CC.Points.Config.Load', $this, array('moduleFieldset' => $moduleFieldset));
    }
} 