<?php

namespace OnlineOrder;


use Menu\API\Menu;
use Menu\Form\MenuItem;
use System\Module\AbstractModule;
use Zend\Db\Adapter\Adapter;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;


class Module extends AbstractModule
{
    const ENTITY_TYPE = 'online-order';
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const APP_ONLINE_ORDER = 'route:app/online-order';
    const APP_ONLINE_ORDER_NEW = 'route:app/online-order/new';
    const APP_ONLINE_ORDER_CHECK_DOMAIN = 'route:app/online-order/check-domain';
    const APP_ONLINE_ORDER_VIEW_FACTOR = 'route:app/online-order/view-factor';

    const ADMIN_ONLINE_ORDER = 'route:admin/online-order';
    const ADMIN_ONLINE_ORDER_WIDGET = 'route:admin/online-order:widget';
    const ADMIN_ONLINE_ORDER_ORDERS = 'route:admin/online-order/orders';
    const ADMIN_ONLINE_ORDER_ORDERS_ALL = 'route:admin/online-order/orders:all';
    const ADMIN_ONLINE_ORDER_ORDERS_EXTENSION = 'route:admin/online-order/orders/extension';
    //const ADMIN_ONLINE_ORDER_ORDERS_NEW = 'route:admin/online-order/orders/new';
    const ADMIN_ONLINE_ORDER_ORDERS_EDIT = 'route:admin/online-order/orders/edit';
    const ADMIN_ONLINE_ORDER_ORDERS_DELETE = 'route:admin/online-order/orders/delete';
    const ADMIN_ONLINE_ORDER_ORDERS_UPDATE = 'route:admin/online-order/orders/update';
    const ADMIN_ONLINE_ORDER_CONFIG = 'route:admin/online-order/config';
    const ADMIN_ONLINE_ORDER_SUB_DOMAINS = 'route:admin/online-order/sub-domains';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('online_order_event_manager')->onLoadMenuTypes($e);
        });
    }
}