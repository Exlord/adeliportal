<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Cron;
return array(
    'service_manager' => array(
        'invokables' => array(
            'cron_api' => 'Cron\API\Cron'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Cron\Controller\Cron' => 'Cron\Controller\CronController',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'cron' => array(
                    'options' => array(
                        'route' => '<site> cron',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Cron',
                            'action' => 'cron'
                        )
                    )
                )
            )
        )
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'cron' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/cron',
                            'defaults' => array(
                                'controller' => 'Cron\Controller\Cron',
                                'action' => 'cron'
                            )
                        )
                    ),
                )
            ),
            'admin' => array(
                'child_routes' => array(
                    'cron' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/cron',
                            'defaults' => array(
                                'controller' => 'Cron\Controller\Cron',
                                'action' => 'config',
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
);