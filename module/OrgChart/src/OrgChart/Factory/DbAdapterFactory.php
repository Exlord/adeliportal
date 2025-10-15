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

class DbAdapterFactory implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        // create your object and set dependencies
//        return $object
    }
} 