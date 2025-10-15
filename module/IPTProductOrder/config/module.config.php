<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace IPTProductOrder;
return array(
    'service_manager' => array(
        'invokables' => array(
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'IPTProductOrder\Controller\Order' => 'IPTProductOrder\Controller\OrderController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'product-order' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/product-order',
                            'defaults' => array(
                                'controller' => 'IPTProductOrder\Controller\Order',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
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
                'IPTProductOrders' => __DIR__ . '/../public',
            ),
        ),
    ),
);