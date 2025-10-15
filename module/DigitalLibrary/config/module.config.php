<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace DigitalLibrary;
return array(
    'service_manager' => array(
        'invokables' => array(
            'book_table' => 'DigitalLibrary\Model\BookTable',
            'dl_event_manager' => 'DigitalLibrary\API\EventManager'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'DigitalLibrary\Controller\Admin' => 'DigitalLibrary\Controller\Admin',
            'DigitalLibrary\Controller\Client' => 'DigitalLibrary\Controller\Client',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'books' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/books',
                            'defaults' => array(
                                'controller' => 'DigitalLibrary\Controller\Client',
                                'action' => 'index'
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'view' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/view/:id/:title',
                                    'defaults' => array(
                                        'controller' => 'DigitalLibrary\Controller\Client',
                                        'action' => 'view'
                                    )
                                ),
                            ),
                            'viewer' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/viewer/:fileId',
                                    'defaults' => array(
                                        'controller' => 'DigitalLibrary\Controller\Client',
                                        'action' => 'viewer'
                                    )
                                ),
                            )
                        )
                    )
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'book' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/book',
                            'defaults' => array(
                                'controller' => 'DigitalLibrary\Controller\Admin',
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
                                        'controller' => 'DigitalLibrary\Controller\Admin',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'DigitalLibrary\Controller\Admin',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'DigitalLibrary\Controller\Admin',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                        )
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'contents' => array(
                'pages' => array(
                    array(
                        'label' => 'Book',
                        'route' => 'admin/book',
                        'resource' => 'route:admin/book',
                        'pages' => array(
                            array(
                                'label' => 'New',
                                'route' => 'admin/book/new',
                                'resource' => 'route:admin/book/new',
                            ),
                            array(
                                'route' => 'admin/book/edit',
                                'visible' => false,
                                'resource' => 'route:admin/book/edit',
                            ),
                            array(
                                'route' => 'admin/book/delete',
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
            'dl_search' => 'DigitalLibrary\View\Helper\Search'
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
                'DigitalLibrary' => __DIR__ . '/../public',
            ),
        ),
    ),
    'fields_entities' => array(
        'books' => 'Books'
    ),
    'components' => array(
        'dl_search' => array(
            'label' => 'Digital Library Search',
            'description' => '',
            'helper' => 'dl_search',
        ),
    ),
);