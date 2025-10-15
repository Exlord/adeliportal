<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace SimpleOrder;
return array(
    'service_manager' => array(
        'invokables' => array(
            'simple_order_table' => 'SimpleOrder\Model\SimpleOrderTable',
            'simple_order_event_manager' => 'SimpleOrder\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'SimpleOrder\Controller\SimpleOrder' => 'SimpleOrder\Controller\SimpleOrderController',
            'SimpleOrder\Controller\SimpleOrderAdmin' => 'SimpleOrder\Controller\SimpleOrderAdminController',
            'SimpleOrder\Controller\StepOrder' => 'SimpleOrder\Controller\StepOrderController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'simple-order' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/simple-order',
                            'defaults' => array(
                                'controller' => 'SimpleOrder\Controller\SimpleOrder',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'step-order' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/step-order',
                            'defaults' => array(
                                'controller' => 'SimpleOrder\Controller\StepOrder',
                                'action' => 'step-order',
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'simple-order' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/simple-order',
                            'defaults' => array(
                                'controller' => 'SimpleOrder\Controller\SimpleOrderAdmin',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'SimpleOrder\Controller\SimpleOrderAdmin',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'SimpleOrder\Controller\SimpleOrderAdmin',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                        )
                    ),
                    'other-step' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/other-step',
                            'defaults' => array(
                                'controller' => 'SimpleOrder\Controller\StepOrder',
                                'action' => 'other-step',
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'orders' => array(
                'label' => 'Orders',
                'route' => 'admin/orders',
                'order' => -9995,
                'resource' => 'route:admin/orders',
                'pages' => array(
                    array(
                        'label' => 'Orders',
                        'route' => 'admin/simple-order',
                        'resource' => 'route:admin/simple-order',
                    ),
                ),
            ),
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'simpleOrder_new_order_form',
                        'route' => 'admin/simple-order/config',
                        'resource' => 'route:admin/simple-order/config',
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
        'invokables' => array(// 'sample_helper' => 'OnlineOrders\View\Helper\OnlineOrders',
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
                'simple-order' => __DIR__ . '/../public',
            ),
        ),
    ),
    'template_placeholders' => array(
        'simpleOrder_new_order_form' => array(
            '__NAME__' => 'Name',
            '__MOBILE__' => 'Mobile',
            '__EMAIL__' => 'Email',
            '__DESCRIPTION__' => 'Description',
            '__ITEMS__' => 'Items',
            '__CREATED__' => 'application_label_reg_date',
        ),
    ),
);