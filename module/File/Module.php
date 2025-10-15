<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace File;

use System\Module\AbstractModule;
use User\Permissions\Acl\Resource\Resource;
use Zend\Db\Adapter\Adapter;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use File\Model;
use User\Permissions\Acl\Acl;


class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const APP_DELETE_FILE = 'route:app/delete-file';

    //const ADMIN_FILE_TYPES = 'route:admin/file-types';
    const ADMIN_FILE = 'route:admin/file';
    const ADMIN_FILE_CONNECTOR = 'route:admin/file/connector';
    const ADMIN_FILE_PUBLIC = 'route:admin/file/public';
    const ADMIN_FILE_PUBLIC_ALL = 'route:admin/file/public:all';
    const ADMIN_FILE_PRIVATE = 'route:admin/file/private';
    const ADMIN_FILE_NEW = 'route:admin/file/new';
    const ADMIN_FILE_EDIT = 'route:admin/file/edit';
    const ADMIN_FILE_DELETE = 'route:admin/file/delete';
    const ADMIN_FILE_UPDATE = 'route:admin/file/update';

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'HumusStreamResponseSender' => __DIR__ . '/src/HumusStreamResponseSender',
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);

        if (PHP_SAPI == 'cli') {
            return;
        }
        $app = $e->getTarget();
        $serviceManager = $app->getServiceManager();
        $streamResponseSender = $serviceManager->get('HumusStreamResponseSender\StreamResponseSender');
        $sharedEventManager = $app->getEventManager()->getSharedManager();
        /* @var $sharedEventManager SharedEventManager */
        $sharedEventManager->attach(
            'Zend\Mvc\SendResponseListener',
            SendResponseEvent::EVENT_SEND_RESPONSE,
            $streamResponseSender
        );
    }
}
