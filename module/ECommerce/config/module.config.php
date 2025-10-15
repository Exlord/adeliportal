<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace ECommerce;
return array(
    'service_manager' => array(
        'invokables' => array(
            'product_table' => 'ECommerce\Model\ProductTable',
            'product_api' => 'ECommerce\API\Product',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ECommerce\Controller\ProductAdmin' => 'ECommerce\Controller\ProductAdminController',
            'ECommerce\Controller\Commerce' => 'ECommerce\Controller\CommerceController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'e-commerce' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/e-commerce',
                            'defaults' => array(
                                'controller' => 'ECommerce\Controller\Commerce',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'product' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/product',
                                    'defaults' => array(
                                        'controller' => 'ECommerce\Controller\ProductAdmin',
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
                                                'controller' => 'ECommerce\Controller\ProductAdmin',
                                                'action' => 'new',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'ECommerce\Controller\ProductAdmin',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'ECommerce\Controller\ProductAdmin',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'admin_menu' => array(
            'E-Commerce' => array(
                'label' => 'E-Commerce',
                'route' => 'admin/e-commerce',
                'resource' => 'route:admin/e-commerce',
                'pages' => array(
                    array(
                        'label' => 'Products',
                        'route' => 'admin/e-commerce/product',
                        'resource' => 'route:admin/e-commerce/product',
                        'pages' => array(
                            array(
                                'label' => 'New Product',
                                'route' => 'admin/e-commerce/product/new',
                                'resource' => 'route:admin/e-commerce/product/new',
                            ),
                            array(
                                'label' => 'Edit Product',
                                'route' => 'admin/e-commerce/edit',
                                'resource' => 'route:admin/e-commerce/product/edit',
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
        'invokables' => array(),
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
                'ECommerce' => __DIR__ . '/../public',
            ),
        ),
    ),
);