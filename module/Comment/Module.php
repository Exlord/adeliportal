<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Comment;

use Comment\Model;
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
    const ENTITY_TYPE = __NAMESPACE__;


    const APP_COMMENT = 'route:app/comment';
    const APP_COMMENT_NEW = 'route:app/comment/new';
    const APP_COMMENT_EDIT = 'route:app/comment/edit';
    const APP_COMMENT_EDIT_ALL = 'route:app/comment/edit:all';
    const APP_COMMENT_DELETE = 'route:app/comment/delete';
    const APP_COMMENT_DELETE_ALL = 'route:app/comment/delete:all';

    const ADMIN_COMMENT = 'route:admin/comment';
    const ADMIN_COMMENT_ALL = 'route:admin/comment:all';
    const ADMIN_COMMENT_NEW = 'route:admin/comment/new';
    const ADMIN_COMMENT_EDIT = 'route:admin/comment/edit';
    const ADMIN_COMMENT_EDIT_ALL = 'route:admin/comment/edit:all';
    const ADMIN_COMMENT_UPDATE = 'route:admin/comment/update';
    const ADMIN_COMMENT_UPDATE_ALL = 'route:admin/comment/update:all';
    const ADMIN_COMMENT_DELETE = 'route:admin/comment/delete';
    const ADMIN_COMMENT_DELETE_ALL = 'route:admin/comment/delete:all';
    const ADMIN_COMMENT_CONFIG = 'route:admin/comment/config';
    const ADMIN_COMMENT_APPROVE = 'route:admin/comment:approve';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);

        if (isAllowed(self::ADMIN_COMMENT_APPROVE)) {
            $em = StaticEventManager::getInstance();

            $class = $this;
            $em->attach('Notify\View\Helper\Notifications', 'Notification.Load.Bar', function (Event $e) use ($class) {
                getSM('comment_event_manager')->onLoadNotificationBar($e);
            });
        }
    }
}


