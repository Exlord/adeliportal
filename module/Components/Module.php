<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Components;

use Application\API\App;
use Application\Model\Config;
use Cron\API\Cron;
use Zend\Db\Adapter\Adapter;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Ldap\Node\RootDse\eDirectory;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ApplicationInterface;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;

class Module extends \System\Module\AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const ADMIN_BLOCK = 'route:admin/block';
    const ADMIN_BLOCK_NEW = 'route:admin/block/new';
    const ADMIN_BLOCK_EDIT = 'route:admin/block/edit';
    const ADMIN_BLOCK_UPDATE = 'route:admin/block/update';
    const ADMIN_BLOCK_DELETE = 'route:admin/block/delete';
}
