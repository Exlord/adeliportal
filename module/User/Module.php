<?php

namespace User;

use Cron\API\Cron;
use Menu\API\Menu;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;


class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const APP_USER_PASSWORD_RECOVERY = 'route:app/user/password-recovery';

    const ADMIN_USER = 'route:admin/users';
    const ADMIN_USER_WIDGET = 'route:admin/users:widget';
    const ADMIN_USER_UPDATE = 'route:admin/users/update';
    const ADMIN_USER_PASSWORD_RESET = 'route:admin/users/password-reset';
    const ADMIN_USER_PERMISSION = 'route:admin/users/permission';
    const ADMIN_USER_PERMISSION_CHANGE = 'route:admin/users/permission/permission-change';
    const ADMIN_USER_ROLE = 'route:admin/users/role';
    const ADMIN_USER_ROLE_DELETE = 'route:admin/users/role/delete';
    const ADMIN_USER_ROLE_NEW = 'route:admin/users/role/new';
    const ADMIN_USER_ROLE_EDIT = 'route:admin/users/role/edit';
    const ADMIN_USER_DELETE = 'route:admin/users/delete';
    const ADMIN_USER_DELETE_ALL = 'route:admin/users/delete:all';
    const ADMIN_USER_DELETE_OWN = 'route:admin/users/delete:own';
    const ADMIN_USER_EDIT_IMAGE = 'route:admin/users/edit-image';
    const ADMIN_USER_EDIT_IMAGE_ALL = 'route:admin/users/edit-image:all';
    const ADMIN_USER_EDIT = 'route:admin/users/edit';
    const ADMIN_USER_EDIT_ROLE = 'route:admin/users/edit:role';
    const ADMIN_USER_EDIT_ALL = 'route:admin/users/edit:all';
    const ADMIN_USER_CHANGE_PASSWORD = 'route:admin/users/change-password';
    const ADMIN_USER_VIEW = 'route:admin/users/view';
    const ADMIN_USER_VIEW_ALL = 'route:admin/users/view:all';
    const ADMIN_USER_NEW = 'route:admin/users/new';
    const ADMIN_USER_CONFIG = 'route:admin/users/config';
    const USER_VIEW_PRIVATE_FIELDS = 'route:admin/users/user-profile:private';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Zend\Mvc\Application', MvcEvent::EVENT_ROUTE, function (Event $e) {
            getSM('user_event_manager')->onRoute($e);
        }, -10000);

        $em->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH_ERROR, function (Event $e) {
            getSM('user_event_manager')->onDispatchError($e);
        }, -5000);

        //CRON
        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            $last_run = $e->getParam('last_run');
            getSM('user_event_manager')->onCronRun($last_run);
        });

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('user_event_manager')->onLoadMenuTypes($e);
        });
    }
}
