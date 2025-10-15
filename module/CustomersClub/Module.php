<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace CustomersClub;

use Application\Model\Config;
use Cron\API\Cron;
use CustomersClub\Model;
use Notify\View\Helper\Notifications;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const NEW_POINT = 'route:admin/customers-club/points/new';
    const EDIT_POINT = 'route:admin/customers-club/points/edit';
    const DELETE_POINT = 'route:admin/customers-club/points/delete';
    const VIEW_CUSTOMER_RECORDS = 'route:admin/customers-club:customer-records';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);

        $em = StaticEventManager::getInstance();
        $class = $this;
        $em->attach('Notify\View\Helper\Notifications', 'Notification.Load.Bar', function (Event $e) use ($class) {
            getSM('cc_event_manager')->onLoadNotificationBar($e);
        });
    }
}


