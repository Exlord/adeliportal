<?php
namespace Contact;

use Application\Model\Config;
use Cron\API\Cron;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use Sample\Model;
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

    const CONTACT_USER_ENTITY_TYPE = 'contact';

    const APP_CONTACT = 'route:app/contact';
    const APP_CONTACT_CATEGORY = 'route:app/contact/category';
    const APP_CONTACT_SINGLE = 'route:app/contact/single';

    const ADMIN_CONTACT = 'route:admin/contact';
    const ADMIN_CONTACT_CONFIG = 'route:admin/contact/config';
    const ADMIN_CONTACT_CONFIG_REPRESENTATIVE = 'route:admin/contact/representative-config';
    const ADMIN_CONTACT_CONTACTS = 'route:admin/contact/contacts';
    const ADMIN_CONTACT_CONTACTS_DELETE = 'route:admin/contact/contacts/delete';
    const ADMIN_CONTACT_CONTACTS_UPDATE = 'route:admin/contact/contacts/update';
    const ADMIN_CONTACT_USER = 'route:admin/contact/user';
    const ADMIN_CONTACT_USER_NEW = 'route:admin/contact/user/new';
    const ADMIN_CONTACT_USER_EDIT = 'route:admin/contact/user/edit';
    const ADMIN_CONTACT_USER_DELETE = 'route:admin/contact/user/delete';
    const ADMIN_CONTACT_USER_UPDATE = 'route:admin/contact/user/update';
    const ADMIN_CONTACT_USER_MENU_USER_LIST = 'route:admin/contact/user/menu-contact-user-list';
    const ADMIN_CONTACT_USER_MENU_CATEGORY_LIST = 'route:admin/contact/user/menu-contact-category-list';


    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);

        $em = StaticEventManager::getInstance();
        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('contact_event_manager')->onLoadMenuTypes($e);
        });
    }
}


