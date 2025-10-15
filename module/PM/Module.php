<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace PM;

use Notify\View\Helper\Notifications;
use PM\API\PM;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);

        $em = StaticEventManager::getInstance();
        $class = $this;
        $em->attach('Notify\View\Helper\Notifications', 'Notification.Load.Bar', function (Event $e) use ($class) {
            getSM('pm_event_manager')->onLoadNotificationBar($e);
        });
    }
}