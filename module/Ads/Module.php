<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Ads;

use Ads\Model\AdsTable;
use Application\API\App;
use Application\Model\Config;
use Category\API\CategoryItem;
use Components\API\Block;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Mail\API\Mail;
use Sample\Model;
use SiteMap\API\SiteMap;
use SiteMap\Model\Url;
use SiteMap\Model\UrlSet;
use System\Module\AbstractModule;
use Theme\API\Common;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;
    const ADS_ENTITY_TYPE = 'ads';
    const ADS_CATEGORY_ENTITY_TYPE = 'ads_category';
    const ADS_KEYWORD_ENTITY_TYPE = 'ads_keyword';


    const ADMIN_AD_LIST_ALL = 'route:admin/ad/list:all';
    const ADMIN_AD_LIST_CHANGE_ALL_FIELD = 'route:admin/ad/list:changeAllField';
    const ADMIN_AD_NEW_PAYMENT = 'route:admin/ad/new:payment';
    const ADMIN_AD_NEW_APPROVED = 'route:admin/ad/new:approved';
    const ADMIN_AD_NEW_REF = 'route:admin/ad/ref/new';

    const APP_AD_VIEW_ALL_INFO = 'route:app/ad/view:allInfo';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();
        $em->attach('Fields', 'Fields.EntityTypes.Load', function (Event $e) {
            getSM('ads_event_manager')->onFieldsEntityTypesLoad($e);
        });

        $em->attach('Components\API\Block', 'Components.Block.Types.Load', function (Event $e) {
            getSM('ads_event_manager')->onComponentsBlockTypesLoad($e);
        });

        $em->attach('Category\API\CategoryItem', CategoryItem::URL_GENERATING, function (Event $e) {
            getSM('ads_event_manager')->onCategoryItemUrlGeneration($e);
        });

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('ads_event_manager')->onLoadBlockConfigs($e);
        });

        $em->attach('SiteMap\API\SiteMap', SiteMap::GENERATING, function (Event $e) {
            getSM('ads_event_manager')->OnSiteMapGeneration($e);
        });

        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            $lastRun = $e->getParam('last_run');
            getSM('ads_event_manager')->onCronRun($lastRun);
        });

        $em->attach('Application\API\EventManager', 'Dashboard.Load', function (Event $e) {
            getSM('ads_event_manager')->onDashboardLoad($e);
        });
    }
}


