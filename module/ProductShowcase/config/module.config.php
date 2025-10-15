<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace ProductShowcase;
return array(
    'service_manager' => array(
        'invokables' => array(
            'product_showcase_table' => 'ProductShowcase\Model\PsTable',
            'ps_cart_table' => 'ProductShowcase\Model\PsCartTable',
            'product_showcase_event_manager' => 'ProductShowcase\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ProductShowcase\Controller\PsAdmin' => 'ProductShowcase\Controller\PsAdminController',
            'ProductShowcase\Controller\Ps' => 'ProductShowcase\Controller\PsController',
            'ProductShowcase\Controller\PsCartAdmin' => 'ProductShowcase\Controller\PsCartAdminController',
            'ProductShowcase\Controller\PsCart' => 'ProductShowcase\Controller\PsCartController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'product-showcase' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/product-showcase',
                            'defaults' => array(
                                'controller' => 'ProductShowcase\Controller\PsAdmin',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'new' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'ProductShowcase\Controller\PsAdmin',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'ProductShowcase\Controller\PsAdmin',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'ProductShowcase\Controller\PsAdmin',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'ProductShowcase\Controller\PsAdmin',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'orders' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/orders',
                                    'defaults' => array(
                                        'controller' => 'ProductShowcase\Controller\PsCartAdmin',
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
                                                'controller' => 'ProductShowcase\Controller\PsCartAdmin',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                    /*'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'ProductShowcase\Controller\PsAdmin',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),*/
                                )
                            ),
                        )
                    ),
                ),
            ),
            'app' => array(
                'child_routes' => array(
                    'product-showcase' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/product-showcase',
                            'defaults' => array(
                                'controller' => 'ProductShowcase\Controller\Ps',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/list[/:tagId/:tagName]',
                                    'defaults' => array(
                                        'controller' => 'ProductShowcase\Controller\Ps',
                                        'action' => 'index',
                                    ),
                                ),
                            ),
                            'category' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/category',
                                    'defaults' => array(
                                        'controller' => 'ProductShowcase\Controller\Ps',
                                        'action' => 'category',
                                    ),
                                ),
                            ),
                            'cart' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/cart',
                                    'defaults' => array(
                                        'controller' => 'ProductShowcase\Controller\PsCart',
                                        'action' => 'cart',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'view' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:orderId/view',
                                            'defaults' => array(
                                                'controller' => 'ProductShowcase\Controller\PsCart',
                                                'action' => 'view',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'view' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/view/:id[/:title]',
                                    'defaults' => array(
                                        'controller' => 'ProductShowcase\Controller\Ps',
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                        )
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'modules' => array(
                'pages' => array(
                    array(
                        'label' => 'PS_PRODUCT_SHOWCASE',
                        'route' => 'admin/product-showcase',
                        'resource' => 'route:admin/product-showcase',
                        'pages' => array(
                            array(
                                'label' => 'PS_NEW_PRODUCT_SHOWCASE',
                                'route' => 'admin/product-showcase/new',
                                'resource' => 'route:admin/product-showcase/new',
                            ),
                            array(
                                'label' => 'APP_ORDERS',
                                'route' => 'admin/product-showcase/orders',
                                'resource' => 'route:admin/product-showcase/orders',
                            ),
                            array(
                                'label' => 'Edit',
                                'route' => 'admin/product-showcase/edit',
                                'resource' => 'route:admin/product-showcase/edit',
                                'visible' => false
                            ),
                        )
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
            'psCart' => 'ProductShowcase\View\Helper\PsCart',
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
            'paths' => array(//'Sample' => __DIR__ . '/../public',
            ),
        ),
    ),
    'fields_entities' => array(
        'product_showcase' => 'PS_PRODUCT_SHOWCASE'
    )
);