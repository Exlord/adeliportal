<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace RealEstate;

use Analyzer\API\Analyzer;
use Application\API\App;
use Application\API\Search;
use Category\API\CategoryItem;
use Components\API\Block;
use Components\Form\NewBlock;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use RealEstate\Model\RealEstateTable;
use SiteMap\API\SiteMap;
use SiteMap\Model\Url;
use SiteMap\Model\UrlSet;
use System\Module\AbstractModule;
use RealEstate\Model;
use Application\Model\Config;
use Cron\API\Cron;
use RSS\Model\ReaderTable;
use Theme\API\Common;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\Form\Fieldset;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    const ENTITY_TYPE = 'real-estate';
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const APP_REAL_ESTATE = 'route:app/real-estate';
    const APP_REAL_ESTATE_SEARCHBYMAP = 'route:app/real-estate/search-by-map';
    const APP_REAL_ESTATE_COMPARE = 'route:app/real-estate/compare';
    const APP_REAL_ESTATE_EXPORT = 'route:app/real-estate/export';
    const APP_REAL_ESTATE_APP_DOWNLOAD = 'route:app/real-estate/app-download:perm';
    const APP_REAL_ESTATE_UPLOAD_APP_DATA = 'route:app/real-estate/upload-app-data';
    const APP_REAL_ESTATE_AGENT = 'route:app/real-estate/agent';
    const APP_REAL_ESTATE_NEW_REQUEST = 'route:app/real-estate/new-request';
    const APP_REAL_ESTATE_EDIT = 'route:app/real-estate/edit';
    const APP_REAL_ESTATE_EDIT_USER = 'route:app/real-estate/edit-user';
    //const APP_REAL_ESTATE_KEYWORD = 'route:app/real-estate/keyword';
    const APP_REAL_ESTATE_NEW_TRANSFER = 'route:app/real-estate/new-transfer';
    const APP_REAL_ESTATE_VIEW = 'route:app/real-estate/view';
    const APP_REAL_ESTATE_VIEW_ALL = 'route:app/real-estate/view:all';
    const APP_REAL_ESTATE_VIEW_ALL_SHOW_USER_INFO = 'route:app/real-estate/view:show-user-info';
    const APP_REAL_ESTATE_VIEW_ALL_SHOW_USER_AGENT_INFO = 'route:app/real-estate/view:show-user-agent-info';
    const APP_REAL_ESTATE_LIST = 'route:app/real-estate/list';
    const APP_REAL_ESTATE_LIST_ALL = 'route:app/real-estate/list:all';


    const ADMIN_REAL_ESTATE = 'route:admin/real-estate';
    const ADMIN_REAL_ESTATE_ALL = 'route:admin/real-estate:all';
    const ADMIN_REAL_ESTATE_AGENT_AREA = 'route:admin/real-estate/agent-area';
    const ADMIN_REAL_ESTATE_AGENT_AREA_GET = 'route:admin/real-estate/agent-area/get-agent-area';
    const ADMIN_REAL_ESTATE_WIDGET = 'route:admin/real-estate:widget';
    //const ADMIN_VIEW_OTHERS_ESTATES_LIST = 'route:admin/real-estate:all';
    const ADMIN_REAL_ESTATE_WORD_EXPORT = 'route:admin/real-estate/exp-word';
    const ADMIN_REAL_ESTATE_UPDATE = 'route:admin/real-estate/update';
    const ADMIN_REAL_ESTATE_UPDATE_ALL = 'route:admin/real-estate/update:all';
    const ADMIN_REAL_ESTATE_ARCHIVE = 'route:admin/real-estate/archive';
    const ADMIN_REAL_ESTATE_ARCHIVE_ALL = 'route:admin/real-estate/archive:all';
    const ADMIN_REAL_ESTATE_VIEW = 'route:admin/real-estate/view';
    const ADMIN_REAL_ESTATE_VIEW_ALL = 'route:admin/real-estate/view:all';
    const ADMIN_REAL_ESTATE_DELETE = 'route:admin/real-estate/delete';
    const ADMIN_REAL_ESTATE_DELETE_ALL = 'route:admin/real-estate/delete:all';
    const ADMIN_REAL_ESTATE_EDIT = 'route:admin/real-estate/edit';
    const ADMIN_REAL_ESTATE_EDIT_ALL = 'route:admin/real-estate/edit:all';
    const ADMIN_REAL_ESTATE_NEW_REQUEST = 'route:admin/real-estate/new-request';
    const ADMIN_REAL_ESTATE_NEW_TRANSFER = 'route:admin/real-estate/new-transfer';
    const ADMIN_REAL_ESTATE_CONFIG = 'route:admin/real-estate/config';
    const ADMIN_REAL_ESTATE_CONFIG_MORE = 'route:admin/real-estate/config/more';
    const ADMIN_REAL_ESTATE_LIST = 'route:admin/real-estate/list';
    const ADMIN_REAL_ESTATE_LIST_ALL = 'route:admin/real-estate/list:all';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Analyzer\API\Analyzer', 'Analyzer.Data.Loading', function (Event $e) {
            getSM('real_estate_event_manager')->onLoadAnalyzerStatisticsData($e);
        });

        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            getSM('real_estate_event_manager')->onCronRun($e);
        });

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('real_estate_event_manager')->onLoadMenuTypes($e);
        });

        $em->attach('Category\API\CategoryItem', CategoryItem::URL_GENERATING, function (Event $e) {
            getSM('real_estate_event_manager')->onCategoryItemUrlGeneration($e);
        });

        $em->attach('SiteMap\API\SiteMap', SiteMap::GENERATING, function (Event $e) {
            getSM('real_estate_event_manager')->OnSiteMapGeneration($e);
        });

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('real_estate_event_manager')->onLoadBlockConfigs($e);
        });

        $em->attach('Application\API\Search', Search::SYSTEM_WIDE_SEARCH, function (Event $e) {
            getSM('real_estate_event_manager')->onSearch($e);
        });

        $em->attach('NewsLetter\API\NewsLetter', 'NewsLetterGetInfo', function (Event $e) {
            getSM('real_estate_event_manager')->OnNewsLetterGetInformation($e);
        });
    }
}