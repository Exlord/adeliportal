<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Sample;

use Application\Model\Config;
use Cron\API\Cron;
use Sample\Model;
use System\Module\AbstractModule;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();
    }
}