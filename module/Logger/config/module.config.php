<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Logger;
return array(
    'controllers' => array(
        'invokables' => array(
            'Logger\Controller\Logger' => 'Logger\Controller\LoggerController',
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'logger' => 'Logger\Model\LogTable',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'reports' => array(
                        'child_routes' => array(
                            'logs' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/logs',
                                    'defaults' => array(
                                        'controller' => 'Logger\Controller\Logger',
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
                                                'controller' => 'Logger\Controller\Logger',
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
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'reports' => array(
                'pages' => array(
                    array(
                        'label' => 'System Logs',
                        'route' => 'admin/reports/logs',
                        'resource' => 'route:admin/reports/logs',
                        'pages' => array(
                            array(
                                'label' => 'Error Logs',
                                'route' => 'admin/reports/logs',
                                'resource' => 'route:admin/reports/logs',
                                'query' => 'grid_filter_priority=4'
                            ),
                            array(
                                'label' => 'Info Logs',
                                'route' => 'admin/reports/logs',
                                'resource' => 'route:admin/reports/logs',
                                'query' => 'grid_filter_priority=6'
                            ),
                        )
                    )
                ),
            ),
        )
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
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),

    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'logger' => __DIR__ . '/../public',
            ),
        ),
    ),
    'Logger' => array(
        'name' => 'Logger',
        'note' => 'Enables Logging of errors and events.',
        'can_be_disabled' => false,
        'client_usable' => false,
        'depends' => array('System'),
        'stage' => 'development', 'version' => '1.0'
    ),
);