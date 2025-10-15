<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Analyzer;
return array(
    'service_manager' => array(
        'invokables' => array(
            'visits_table' => 'Analyzer\Model\VisitsTable',
            'visits_archive_table' => 'Analyzer\Model\VisitsArchiveTable',
            'analyzer_api' => 'Analyzer\API\Analyzer',
            'analyzer_event_manager' => 'Analyzer\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Analyzer\Controller\Admin' => 'Analyzer\Controller\Admin',
            'Analyzer\Controller\Analyzer' => 'Analyzer\Controller\Analyzer',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'configs' => array(
                        'child_routes' => array(
                            'analyzer' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/analyzer',
                                    'defaults' => array(
                                        'controller' => 'Analyzer\Controller\Admin',
                                        'action' => 'config',
                                    ),
                                ),
                            )
                        )
                    ),
                    'reports' => array(
                        'child_routes' => array(
                            'analyzer' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/analyzer',
                                    'defaults' => array(
                                        'controller' => 'Analyzer\Controller\Admin',
                                        'action' => 'report',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'more-data' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/more-data',
                                            'defaults' => array(
                                                'controller' => 'Analyzer\Controller\Admin',
                                                'action' => 'more-data',
                                            ),
                                        ),
                                    )
                                )
                            )
                        )
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Analyzer',
                        'route' => 'admin/configs/analyzer',
                        'resource' => 'route:admin/configs/analyzer',
                    )
                ),
            ),
            'reports' => array(
                'pages' => array(
                    array(
                        'label' => 'Analyzer',
                        'route' => 'admin/reports/analyzer',
                        'resource' => 'route:admin/reports/analyzer',
                    )
                )
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
            'system_status_block' => 'Analyzer\View\Helper\SystemStatusBlock'
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
                'analyzer' => __DIR__ . '/../public',
            ),
        ),
    ),
    'components' => array(
        'system_status_block' => array(
            'label' => 'System Status',
            'description' => 'including but not limited to visitors count',
            'helper' => 'system_status_block',
        ),
    ),
);