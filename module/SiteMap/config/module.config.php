<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace SiteMap;
return array(
    'service_manager' => array(
        'invokables' => array(
            'sitemap_api' => 'SiteMap\API\SiteMap',
            'sitemap_event_manager' => 'SiteMap\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'SiteMap\Controller\SiteMap' => 'SiteMap\Controller\SitemapController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'sitemap' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/sitemap.xml',
                    'defaults' => array(
                        'controller' => 'SiteMap\Controller\SiteMap',
                        'action' => 'index',
                    ),
                ),
            ),
            'app' => array(
                'child_routes' => array(
                    'sitemap' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/sitemap',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'SiteMap\Controller\SiteMap',
                                'action' => 'sitemap',
                            ),
                        ),
                    ),
                    //old don't use this
                    'site-map' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/sitemap',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'SiteMap\Controller\SiteMap',
                                'action' => 'sitemap',
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'sitemap' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/sitemap',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'SiteMap\Controller\SiteMap',
                                'action' => 'config',
                            ),
                        ),
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
                        'label' => 'SITEMAP',
                        'route' => 'admin/sitemap',
                        'resource' => 'route:admin/sitemap',
                    ),
                )
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
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
                'sitemap' => __DIR__ . '/../public',
            ),
        ),
    ),
);