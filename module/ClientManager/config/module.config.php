<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace ClientManager;
return array(
    'service_manager' => array(
        'invokables' => array(
            'license_table' => 'ClientManager\Model\LicenseTable',
            'license_api' => 'ClientManager\API\License'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ClientManager\Controller\Update' => 'ClientManager\Controller\UpdateController',
            'ClientManager\Controller\License' => 'ClientManager\Controller\LicenseController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'check-for-update' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/check-for-update',
                    'defaults' => array(
                        'controller' => 'ClientManager\Controller\Update',
                        'action' => 'check-for-update',
                    ),
                ),
            ),
            'get-update-file' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/get-update-file/:type/:file/:hash',
                    'defaults' => array(
                        'controller' => 'ClientManager\Controller\Update',
                        'action' => 'get-update-file',
                    ),
                ),
            ),
            'app' => array(
                'child_routes' => array(
                    'make-license' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/make-license',
                            'defaults' => array(
                                'controller' => 'ClientManager\Controller\License',
                                'action' => 'make-with-license',
                            ),
                        ),
                    ),
                )
            ),
            'admin' => array(
                'child_routes' => array(
                    'make-license' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/make-license',
                            'defaults' => array(
                                'controller' => 'ClientManager\Controller\License',
                                'action' => 'make-with-acl',
                            ),
                        ),
                    ),
                )
            )
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
               // 'OnlineOrders' => __DIR__ . '/../public',
            ),
        ),
    ),
);