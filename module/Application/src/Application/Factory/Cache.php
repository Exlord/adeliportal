<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/8/13
 * Time: 1:06 PM
 */

namespace Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache\StorageFactory;

class Cache implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->get('ApplicationConfig');
        return StorageFactory::factory($config['cache']);
    }
} 