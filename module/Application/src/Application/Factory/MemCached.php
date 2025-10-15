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

class MemCached implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cache = StorageFactory::factory(
            array(
                'adapter' => array(
                    'name' => 'Memcached',
                    'options' => array(
                        'ttl' => 604800,
                        'resourceId' => ACTIVE_SITE,
                        'namespace' => ACTIVE_SITE,
                        'servers' => 'localhost',
                        'persistent_id' => 'IPT_SYSTEM',
                    ),
                ),

            )
        );
        return $cache;
    }
}