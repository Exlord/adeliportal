<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/8/13
 * Time: 1:06 PM
 */

namespace Application\Factory;

use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DbAdapter implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('ApplicationConfig');
        try {
            $adapter = new Adapter($config['db']);
            if (!$adapter->getDriver()->getConnection()->isConnected())
                $adapter->getDriver()->getConnection()->connect();
        } catch (\Exception $e) {
            die($e->getCode() . ' ' . $e->getMessage());
        }
        return $adapter;
    }
} 