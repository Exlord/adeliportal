<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace OnlineOrders;
return array(
    'controllers' => array(
        'invokables' => array(
            'OnlineOrders\Controller\OnlineOrders' => 'OnlineOrders\Controller\OnlineOrdersController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'online-orders' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/online-orders',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'out-put' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/out-put',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'out-put',
                                    ),
                                ),
                            ),
                            'final-part-order' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/:typePayment/:refCode/:sumResultPrice/final-part-order',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'final-part-order',
                                    ),
                                ),
                            ),
                            'order-tracking' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/order-tracking',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'order-tracking',
                                    ),
                                ),
                            ),
                            'order-print' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/order-print',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'factorprew',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'search-domain' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/:domain/search-domain',
                            'defaults' => array(
                                'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                'action' => 'search-domain',
                            ),
                        ),
                    ),
                    'create-captcha' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/create-captcha',
                            'defaults' => array(
                                'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                'action' => 'create-captcha-code',
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'online-orders' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/online-orders',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                'action' => 'group-select',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'group-select' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/group-select',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'group-select',

                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'group-operations' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:type/group-operations',
                                            'defaults' => array(
                                                'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                                'action' => 'group-operations',

                                            ),
                                        ),
                                    ),
                                ),
                            ),

                            'language-select' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/language-select',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'language-select',

                                    ),
                                ),
                            ),
                            'account-number' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/account-number',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'account-number',

                                    ),
                                ),
                            ),
                            'item-select' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/item-select',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'item-select',

                                    ),
                                ),
                            ),
                            'order-list' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/order-list',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'order-list',

                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'view-orders' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/view-orders',
                                            'defaults' => array(
                                                'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                                'action' => 'view-orders',

                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array(
                                            'confirmation-orders' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/:domainName/:domainType/confirmation-orders',
                                                    'defaults' => array(
                                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                                        'action' => 'confirmation-orders',

                                                    ),
                                                ),
                                            ),
                                            'print-contract' => array(
                                                'type' => 'Literal',
                                                'options' => array(
                                                    'route' => '/print-contract',
                                                    'defaults' => array(
                                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                                        'action' => 'print-contract',

                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),

                            'per-domains' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/per-domains',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'per-domains',

                                    ),
                                ),
                            ),
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'OnlineOrders\Controller\OnlineOrders',
                                        'action' => 'config',

                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'onlineOrders' => array(
                'label' => 'Online Orders',
                'uri' => '#onlineOrders',
                'pages' => array(
                    array(
                        'label' => 'Groups',
                        'route' => 'admin/online-orders/group-select',
                        'resource' => 'route:admin/online-orders/group-select',
                    ),
                    array(
                        'label' => 'Items',
                        'route' => 'admin/online-orders/item-select',
                        'resource' => 'route:admin/online-orders/item-select',
                    ),
                    array(
                        'label' => 'Languages',
                        'route' => 'admin/online-orders/language-select',
                        'resource' => 'route:admin/online-orders/language-select',
                    ),
                    array(
                        'label' => 'Orders',
                        'route' => 'admin/online-orders/order-list',
                        'resource' => 'route:admin/online-orders/order-list',
                    ),
                    array(
                        'label' => 'Private Domain Registration',
                        'route' => 'admin/online-orders/per-domains',
                        'resource' => 'route:admin/online-orders/per-domains',
                    ),
                    array(
                        'label' => 'Account Number',
                        'route' => 'admin/online-orders/account-number',
                        'resource' => 'route:admin/online-orders/account-number',
                    ),
                    array(
                        'label' => 'Config',
                        'route' => 'admin/online-orders/config',
                        'resource' => 'route:admin/online-orders/config',
                    )
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'group_helper' => 'OnlineOrders\View\Helper\GroupForm',
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/' . __NAMESPACE__ . '.lang',
            ),
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'online-orders' => __DIR__ . '/../public',
            ),
        ),
    ),
);