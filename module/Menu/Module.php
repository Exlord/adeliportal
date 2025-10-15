<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Menu;

use Application\API\App;
use Components\API\Block;
use Components\Form\NewBlock;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use Menu\Model\MenuTable;
use SiteMap\API\SiteMap;
use SiteMap\Model\Url;
use SiteMap\Model\UrlSet;
use System\API\BaseAPI;
use System\Module\AbstractModule;
use Theme\API\Common;
use Zend\Db\Adapter\Adapter;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Form\Fieldset;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const ADMIN_MENU = 'route:admin/menu';
    const ADMIN_MENU_NEW = 'route:admin/menu/new';
    const ADMIN_MENU_EDIT = 'route:admin/menu/edit';
    const ADMIN_MENU_DELETE = 'route:admin/menu/delete';
    const ADMIN_MENU_ITEMS = 'route:admin/menu/items';
    const ADMIN_MENU_ITEMS_NEW = 'route:admin/menu/items/new';
    const ADMIN_MENU_ITEMS_EDIT = 'route:admin/menu/items/edit';
    const ADMIN_MENU_ITEMS_DELETE = 'route:admin/menu/items/delete';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('menu_event_manager')->onLoadBlockConfigs($e);
        });

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('menu_event_manager')->onLoadMenuTypes($e);
        });

        $em->attach('SiteMap\API\SiteMap', SiteMap::GENERATING, function (Event $e) {
            getSM('menu_event_manager')->OnSiteMapGeneration($e);
        });
    }
}