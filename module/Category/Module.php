<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Category;

use Components\API\Block;
use Components\Form\NewBlock;
use Zend\Db\Adapter\Adapter;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;


class Module extends \System\Module\AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const ENTITY_TYPE_CATEGORY = 'category';
    const ENTITY_TYPE_CATEGORY_ITEM = 'category-item';

    const ADMIN_CATEGORY_LIST = 'route:admin/category-list';
    const ADMIN_CATEGORY = 'route:admin/category';
    const ADMIN_CATEGORY_ITEMS_LIST = 'route:admin/category/items-list';
    const ADMIN_CATEGORY_ITEMS_GET_ITEM_LIST = 'route:admin/category/get-item-list';
    const ADMIN_CATEGORY_ITEMS_GET_CATEGORY_LIST = 'route:admin/category/get-category-list';
    const ADMIN_CATEGORY_ITEMS = 'route:admin/category/items';
    const ADMIN_CATEGORY_ITEMS_UPDATE = 'route:admin/category/items/update';
    const ADMIN_CATEGORY_ITEMS_NEW = 'route:admin/category/items/new';
    const ADMIN_CATEGORY_ITEMS_EDIT = 'route:admin/category/items/edit';
    const ADMIN_CATEGORY_ITEMS_DELETE = 'route:admin/category/items/delete';
    const ADMIN_CATEGORY_NEW = 'route:admin/category/new';
    const ADMIN_CATEGORY_EDIT = 'route:admin/category/edit';
    const ADMIN_CATEGORY_DELETE = 'route:admin/category/delete';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('category_event_manager')->onLoadBlockConfigs($e);
        });
    }
}
