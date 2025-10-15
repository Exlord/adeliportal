<?php

namespace OnlineOrder;
return array(
    'service_manager' => array(
        'invokables' => array(
            'customer_table' => 'OnlineOrder\Model\CustomerTable',
            'site_table' => 'OnlineOrder\Model\SiteTable',
            'client_table' => 'OnlineOrder\Model\ClientTable',
            'online_order_event_manager' => 'OnlineOrder\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'OnlineOrder\Controller\OnlineOrder' => 'OnlineOrder\Controller\OnlineOrderController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'online-order' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/online-order',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'new' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'online-order-validate' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/online-order-validate[/:params]',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                        'action' => 'online-order-validate',
                                    ),
                                ),
                            ),
                            'check-domain' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/check-domain',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                        'action' => 'check-domain',
                                    ),
                                ),
                            ),
                            'view-factor' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/view-factor',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                        'action' => 'view-factor',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'online-order' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/online-order',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                'action' => 'orders',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'orders' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/orders',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                        'action' => 'orders',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                                'action' => 'new',

                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                                'action' => 'edit',

                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                                'action' => 'delete',

                                            ),
                                        ),
                                    ),
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                    'extension' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/extension',
                                            'defaults' => array(
                                                'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                                'action' => 'extension',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                            'sub-domains' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/sub-domains',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'OnlineOrder\Controller\OnlineOrder',
                                        'action' => 'sub-domains',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'admin_menu' => array(
            'OnlineOrder' => array(
                'label' => 'Online Order',
                'route' => 'admin/online-order/orders',
                'resource' => 'route:admin/online-order/orders',
                'pages' => array(
                    array(
                        'label' => 'Orders',
                        'route' => 'admin/online-order/orders',
                        'resource' => 'route:admin/online-order/orders',
                    ),
                    array(
                        'label' => 'Sub Domains',
                        'route' => 'admin/online-order/sub-domains',
                        'resource' => 'route:admin/online-order/sub-domains',
                    ),
                    array(
                        'label' => 'Config',
                        'route' => 'admin/online-order/config',
                        'resource' => 'route:admin/online-order/config',
                    ),
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
        'invokables' => array( // 'group_helper' => 'OnlineOrder\View\Helper\GroupForm',
            'onlineOrder_widget' => 'OnlineOrder\View\Helper\Widget',
        ),
    ),
    'widgets' => array(
        'OnlineOrder\View\Helper\Widget' => 'Online Order Widget'
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
                'OnlineOrder' => __DIR__ . '/../public',
            ),
        ),
    ),
    'OnlineOrder' => array(
        'name' => 'OnlineOrder Module',
        'note' => 'A OnlineOrder Module',
        'can_be_disabled' => true,
        'client_usable' => true,
        'depends' => array('System','Payment'),
        'stage' => 'development','version'=>'1.0'
    ),
    'template_placeholders' => array(
        'Online Order' => array(
            '__TIME__' => 'exp : 15:30:25',
            '__CONTENT__' => 'the default content placeholder'
        )
    )
);