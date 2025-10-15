<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace ServerManager;
return array(
    'service_manager' => array(
        'invokables' => array(),
    ),
    'controllers' => array(
        'invokables' => array(
            'ServerManager\Controller\Test' => 'ServerManager\Controller\TestController',
            'ServerManager\Controller\ServerManager' => 'ServerManager\Controller\ServerManagerController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'server-test' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/server-test',
                            'defaults' => array(
                                'controller' => 'ServerManager\Controller\Test',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'configs' => array(
                'pages' => array(

                )
            )
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
                'server' => __DIR__ . '/../public',
            ),
        ),
    ),
);