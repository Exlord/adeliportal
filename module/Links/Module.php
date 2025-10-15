<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Links;

use Application\API\App;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use SiteMap\API\SiteMap;
use SiteMap\Model\Url;
use SiteMap\Model\UrlSet;
use System\Module\AbstractModule;
use Theme\API\Common;
use Zend\Db\Adapter\Adapter;
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

    const ADMIN_LINKS_LIST = 'route:admin/links-list';
    const ADMIN_LINKS = 'route:admin/links';
    const ADMIN_LINKS_UPDATE = 'route:admin/links/update';
    const ADMIN_LINKS_NEW = 'route:admin/links/new';
    const ADMIN_LINKS_EDIT = 'route:admin/links/edit';
    const ADMIN_LINKS_DELETE = 'route:admin/links/delete';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('links_event_manager')->onLoadMenuTypes($e);
        });

        $em->attach('SiteMap\API\SiteMap', SiteMap::GENERATING, function (Event $e){
            getSM('links_event_manager')->OnSiteMapGeneration($e);
        });
    }
}