<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace ProductShowcase;

use Analyzer\API\Analyzer;
use Application\API\App;
use Components\Form\NewBlock;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use SiteMap\Model\Url;
use SiteMap\Model\UrlSet;
use System\Module\AbstractModule;
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
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const PS_ENTITY_TYPE = 'product_showcase';

    const ADMIN_PRODUCT_SHOWCASE = 'route:admin/product-showcase';
    const ADMIN_PRODUCT_SHOWCASE_NEW = 'route:admin/product-showcase/new';
    const ADMIN_PRODUCT_SHOWCASE_EDIT = 'route:admin/product-showcase/edit';
    const ADMIN_PRODUCT_SHOWCASE_DELETE = 'route:admin/product-showcase/delete';
    const ADMIN_PRODUCT_SHOWCASE_UPDATE = 'route:admin/product-showcase/update';
    const ADMIN_PRODUCT_SHOWCASE_ORDERS = 'route:admin/product-showcase/orders';
    const ADMIN_PRODUCT_SHOWCASE_ORDERS_ALL = 'route:admin/product-showcase/orders:all';
    const ADMIN_PRODUCT_SHOWCASE_ORDERS_DEL = 'route:admin/product-showcase/orders/delete';
    const ADMIN_PRODUCT_SHOWCASE_ORDERS_DEL_ALL = 'route:admin/product-showcase/orders/delete:all';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('product_showcase_event_manager')->onLoadMenuTypes($e);
        });

        $em->attach('Localization\API\Translation', 'translation.dynamicEntityTypes', function (Event $e) {
            getSM('product_showcase_event_manager')->onTranslationDynamicEntityTypes($e);
        });
    }
}