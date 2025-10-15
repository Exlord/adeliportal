<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Theme;
return array(
    'service_manager' => array(
        'invokables' => array(
            'theme_table' => 'Theme\Model\ThemeTable',
            'theme_api' => 'Theme\API\Themes',
            'theme_event_manager' => 'Theme\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Theme\Controller\Theme' => 'Theme\Controller\ThemeController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'themes' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/themes',
                            'defaults' => array(
                                'controller' => 'Theme\Controller\Theme',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'set-default' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name/set-default',
                                    'defaults' => array(
                                        'controller' => 'Theme\Controller\Theme',
                                        'action' => 'set-default',
                                    ),
                                ),
                            ),
                            'config' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'Theme\Controller\Theme',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                        )
                    ),
                )
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'admin' => array(
                'pages' => array(
                    array(
                        'label' => 'Website Themes',
                        'route' => 'admin/themes',
                        'resource' => 'route:admin/themes',
//                        'pages' => array(
//                            array(
//                                'label' => 'Config',
//                                'route' => 'admin/themes/config',
//                                'resource' => 'route:admin/themes/config',
//                            )
//                        )
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
            'partial_view_block' => 'Theme\View\Helper\Partial'
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
                'theme' => __DIR__ . '/../public',
            ),
        ),
    ),
    'components' => array(
        'custom_template_file' => array(
            'label' => 'Custom Template File',
            'description' => 'Renders a Custom Template File from themes folder',
            'helper' => 'partial_view_block',
        ),
    ),
);