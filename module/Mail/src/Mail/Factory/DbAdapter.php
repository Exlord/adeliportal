<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/8/13
 * Time: 1:06 PM
 */

namespace Mail\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DbAdapter implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->get('Config');
        $adapter = new \Zend\Db\Adapter\Adapter($config['mail_db']);
        return $adapter;
    }
} 