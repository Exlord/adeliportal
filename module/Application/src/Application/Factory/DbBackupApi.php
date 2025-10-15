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
use Application\API\Backup\Db;

class DbBackupApi implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        return new Db($serviceLocator->get('db_adapter'));
    }
} 