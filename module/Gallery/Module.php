<?php
namespace Gallery;

use Application\API\App;
use Application\Model\Config;
use Components\API\Block;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Gallery\Model;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use System\Module\AbstractModule;
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

    const ADMIN_BANNER = 'route:admin/banner';
    const ADMIN_BANNER_WIDGET = 'route:admin/banner:widget';
    const ADMIN_BANNER_CONFIGS = 'route:admin/banner/configs';
    const ADMIN_BANNER_EXTENSION = 'route:admin/banner/extension';
    const ADMIN_BANNER_LIST = 'route:admin/banner/list';
    const ADMIN_BANNER_LIST_ALL = 'route:admin/banner/list:all';
    const ADMIN_BANNER_LIST_EDIT = 'route:admin/banner/list/order-banner-edit';
    const ADMIN_BANNER_LIST_DELETE_IMAGE = 'route:admin/banner/list/delete-image';
    const ADMIN_BANNER_LIST_DELETE = 'route:admin/banner/list/delete';
    const ADMIN_BANNER_LIST_UPDATE = 'route:admin/banner/list/update';
    const ADMIN_BANNER_GROUPS = 'route:admin/banner/groups';
    const ADMIN_BANNER_GROUPS_NEW = 'route:admin/banner/groups/new';
    const ADMIN_BANNER_GROUPS_EDIT = 'route:admin/banner/groups/edit';
    const ADMIN_BANNER_GROUPS_DELETE = 'route:admin/banner/groups/delete';
    const ADMIN_BANNER_GROUPS_UPDATE = 'route:admin/banner/groups/update';
    const ADMIN_BANNER_ITEM = 'route:admin/banner/item';
    const ADMIN_BANNER_ITEM_NEW = 'route:admin/banner/item/new';
    const ADMIN_BANNER_ITEM_EDIT = 'route:admin/banner/item/edit';
    const ADMIN_BANNER_ITEM_DELETE = 'route:admin/banner/item/delete';
    const ADMIN_BANNER_ITEM_UPDATE = 'route:admin/banner/item/update';

    const ADMIN_GALLERY = 'route:admin/gallery';
    const ADMIN_GALLERY_GROUPS = 'route:admin/gallery/groups';
    const ADMIN_GALLERY_GROUPS_NEW = 'route:admin/gallery/groups/new';
    const ADMIN_GALLERY_GROUPS_EDIT = 'route:admin/gallery/groups/edit';
    const ADMIN_GALLERY_GROUPS_DELETE = 'route:admin/gallery/groups/delete';
    const ADMIN_GALLERY_GROUPS_UPDATE = 'route:admin/gallery/groups/update';
    const ADMIN_GALLERY_ITEM = 'route:admin/gallery/item';
    const ADMIN_GALLERY_ITEM_NEW = 'route:admin/gallery/item/new';
    const ADMIN_GALLERY_ITEM_EDIT = 'route:admin/gallery/item/edit';
    const ADMIN_GALLERY_ITEM_DELETE = 'route:admin/gallery/item/delete';
    const ADMIN_GALLERY_ITEM_UPDATE = 'route:admin/gallery/item/update';

    const ADMIN_SLIDER = 'route:admin/slider';
    const ADMIN_SLIDER_GROUPS = 'route:admin/slider/groups';
    const ADMIN_SLIDER_GROUPS_NEW = 'route:admin/slider/groups/new';
    const ADMIN_SLIDER_GROUPS_EDIT = 'route:admin/slider/groups/edit';
    const ADMIN_SLIDER_GROUPS_DELETE = 'route:admin/slider/groups/delete';
    const ADMIN_SLIDER_GROUPS_UPDATE = 'route:admin/slider/groups/update';
    const ADMIN_SLIDER_ITEM = 'route:admin/slider/item';
    const ADMIN_SLIDER_ITEM_NEW = 'route:admin/slider/item/new';
    const ADMIN_SLIDER_ITEM_EDIT = 'route:admin/slider/item/edit';
    const ADMIN_SLIDER_ITEM_DELETE = 'route:admin/slider/item/delete';
    const ADMIN_SLIDER_ITEM_UPDATE = 'route:admin/slider/item/update';

    const ADMIN_IMAGE_BOX = 'route:admin/imageBox';
    const ADMIN_IMAGE_BOX_GROUPS = 'route:admin/imageBox/groups';
    const ADMIN_IMAGE_BOX_GROUPS_NEW = 'route:admin/imageBox/groups/new';
    const ADMIN_IMAGE_BOX_GROUPS_EDIT = 'route:admin/imageBox/groups/edit';
    const ADMIN_IMAGE_BOX_GROUPS_DELETE = 'route:admin/imageBox/groups/delete';
    const ADMIN_IMAGE_BOX_GROUPS_UPDATE = 'route:admin/imageBox/groups/update';
    const ADMIN_IMAGE_BOX_ITEM = 'route:admin/imageBox/item';
    const ADMIN_IMAGE_BOX_ITEM_NEW = 'route:admin/imageBox/item/new';
    const ADMIN_IMAGE_BOX_ITEM_EDIT = 'route:admin/imageBox/item/edit';
    const ADMIN_IMAGE_BOX_ITEM_DELETE = 'route:admin/imageBox/item/delete';
    const ADMIN_IMAGE_BOX_ITEM_UPDATE = 'route:admin/imageBox/item/update';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);

        $em = StaticEventManager::getInstance();

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('gallery_event_manager')->onLoadBlockConfigs($e);
        });

        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            $lastRun = $e->getParam('last_run');
            getSM('gallery_event_manager')->onCronRun($lastRun);
        });

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('gallery_event_manager')->onLoadMenuTypes($e);
        });
    }
}