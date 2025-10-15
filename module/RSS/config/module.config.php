<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace RSS;
return array(
    'service_manager' => array(
        'invokables' => array(
            // Keys are the service names
            // Values are valid class names to instantiate.
            'rss_reader_table' => 'RSS\Model\ReaderTable',
            'rss_reader_event_manager' => 'RSS\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'RSS\Controller\Reader' => 'RSS\Controller\ReaderController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                )
            ),
            'admin' => array(
                'child_routes' => array(
                    'rss-reader' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/rss-reader',
                            'defaults' => array(
                                'controller' => 'RSS\Controller\Reader',
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
                                        'controller' => 'RSS\Controller\Reader',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'RSS\Controller\Reader',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'RSS\Controller\Reader',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'RSS\Controller\Reader',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            /*'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'RSS\Controller\Reader',
                                        'action' => 'config',
                                    ),
                                ),
                            ),*/
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
                        'label' => 'RSS Reader',
                        'route' => 'admin/rss-reader',
                        'resource' => 'route:admin/rss-reader',
                        'pages' => array(
                            array(
                                'route' => 'admin/rss-reader/new',
                                'resource' => 'route:admin/rss-reader/new',
                                'label' => 'New RSS',
                            ),
                            array(
                                'route' => 'admin/rss-reader/edit',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/rss-reader/delete',
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
        'invokables' => array(
            'rss_reader' => 'RSS\View\Helper\Reader',
        ),
    ),
    'components' => array(
        'rss_reader_block' => array(
            'label' => 'RSS Reader',
            'description' => 'Fetch and display content for a rss feed',
            'helper' => 'rss_reader',
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
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'rss' => __DIR__ . '/../public',
            ),
        ),
    ),
    'RSS' => array(
        'name' => 'RSS Reader',
        'note' => 'A reader for the RSS content throw out the web',
        'can_be_disabled' => true,
        'client_usable' => true,
        'depends' => array('System'),
        'stage' => 'development', 'version' => '1.0'
    ),
);