<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Page;

use Application\API\App;
use Application\API\Search;
use Application\Model\Config;
use Category\API\CategoryItem;
use Components\API\Block;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use NewsLetter\API\NewsLetter;
use Page\Model\PageTable;
use Page;
use Page\Model\PageTagsTable;
use SiteMap\API\SiteMap;
use SiteMap\Model\Url;
use SiteMap\Model\UrlSet;
use System\Module\AbstractModule;
use Theme\API\Common;
use Theme\API\Themes;
use Zend\Db\Adapter\Adapter;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const PAGE_ENTITY_TYPE = 'page';
    const CONTENT_ENTITY_TYPE = 'content';

    const ADMIN_PAGE_LIST = 'route:admin/page-list';
    const ADMIN_PAGE = 'route:admin/page';
    const ADMIN_PAGE_WIDGET = 'route:admin/page:widget';
    const ADMIN_CONTENT = 'route:admin/content';

    //const ADMIN_AUTO_COMPLETE = 'route:admin/auto-complete';
    const ADMIN_PAGE_UPDATE = 'route:admin/page/update';
    const ADMIN_PAGE_CONFIG = 'route:admin/page-config';
    const ADMIN_PAGE_UPDATE_ALL = 'route:admin/page/update:all';
    const ADMIN_PAGE_NEW = 'route:admin/page/new';
    const ADMIN_PAGE_EDIT = 'route:admin/page/edit';
    const ADMIN_PAGE_EDIT_ALL = 'route:admin/page/edit:all';
    const ADMIN_PAGE_DELETE = 'route:admin/page/delete';
    const ADMIN_PAGE_DELETE_ALL = 'route:admin/page/delete:all';
    const ADMIN_CONTENT_UPDATE = 'route:admin/content/update';
    const ADMIN_CONTENT_UPDATE_ALL = 'route:admin/content/update:all';
    const ADMIN_CONTENT_NEW = 'route:admin/content/new';
    const ADMIN_CONTENT_EDIT = 'route:admin/content/edit';
    const ADMIN_CONTENT_EDIT_ALL = 'route:admin/content/edit:all';
    const ADMIN_CONTENT_DELETE = 'route:admin/content/delete';
    const ADMIN_CONTENT_DELETE_ALL = 'route:admin/content/delete:all';

    const APP_PAGE_VIEW = 'route:app/page-view';

    const APP_CONTENT = 'route:app/content';
    const APP_SINGLE_CONTENT = 'route:app/single-content';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('page_event_manager')->onLoadMenuTypes($e);
        });

        $em->attach('SiteMap\API\SiteMap', SiteMap::GENERATING, function (Event $e) {
            getSM('page_event_manager')->OnSiteMapGeneration($e);
        });

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('page_event_manager')->onLoadBlockConfigs($e);
        });

        $em->attach('Category\API\CategoryItem', CategoryItem::URL_GENERATING, function (Event $e) {
            getSM('page_event_manager')->onCategoryItemUrlGeneration($e);
        });

        $em->attach('Application\API\Search', Search::SYSTEM_WIDE_SEARCH, function (Event $e) {
            getSM('page_event_manager')->onSearch($e);
        });

        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            $lastRun = $e->getParam('last_run');
            getSM('page_event_manager')->onCronRun($lastRun);
        });

        $em->attach('NewsLetter\API\NewsLetter', 'NewsLetterGetInfo', function (Event $e) {
            getSM('page_event_manager')->OnNewsLetterGetInformation($e);
        });
    }
}