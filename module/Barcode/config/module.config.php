<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Barcode;
return array(
    'service_manager' => array(
        'invokables' => array(),

    ),
    'controllers' => array(
        'invokables' => array(
            'Barcode\Controller\Barcode' => 'Barcode\Controller\BarcodeController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'barcode' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/barcode/:barcode',
                    'defaults' => array(
                        'controller' => 'Barcode\Controller\Barcode',
                        'action' => 'get',
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(),
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
                'barcode' => __DIR__ . '/../public',
            ),
        ),
    ),
);