<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Links;
return array(
    'service_manager' => array(
        'invokables' => array(
            'links_table' => 'Links\Model\LinksItemTable',
            'links_event_manager' => 'Links\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Links\Controller\Items' => 'Links\Controller\ItemsController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'links' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/links',
                            'defaults' => array(
                                'controller' => 'Links\Controller\Items',
                                'action' => 'view',
                                'catId' => 0
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'category' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:catId/:catName',
                                    'defaults' => array(
                                        'controller' => 'Links\Controller\Items',
                                        'action' => 'view',
                                    ),
                                ),
                            )
                        )
                    )
                )
            ),
            'admin' => array(
                'child_routes' => array(
                    /* 'configs' => array(
                         'child_routes' => array(
                             'links' => array(
                                 'type' => 'Literal',
                                 'options' => array(
                                     'route' => '/links',
                                     'defaults' => array(
                                         'controller' => 'Links\Controller\Items',
                                         'action' => 'config',
                                     ),
                                 ),
                             )
                         )
                     ),*/
                    'links-list' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/links[/:page]',
                            'constraints' => array(
                                'catId' => '[0-9]+',
                                'page' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Links\Controller\Items',
                                'action' => 'index',
                                'page' => 1
                            ),
                        ),
                    ),
                    'menu-links-category-list' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/menu-links-category-list',
                            'defaults' => array(
                                'controller' => 'Links\Controller\Items',
                                'action' => 'category-list',
                            ),
                        ),
                    ),
                    'links' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/links',
                            'constraints' => array(
                                'catId' => '[0-9]+',
                                'parentId' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Links\Controller\Items',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'Links\Controller\Items',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'Links\Controller\Items',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Links\Controller\Items',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'Links\Controller\Items',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'contents' => array(
                'pages' => array(
                    array(
                        'label' => 'Links',
                        'route' => 'admin/links',
                        'resource' => 'route:admin/links',
                        'pages' => array(
                            array(
                                'label' => 'New',
                                'route' => 'admin/links/new',
                                'resource' => 'route:admin/links/new',
                            ),
                            array(
                                'label' => 'Edit',
                                'route' => 'admin/links/edit',
                                'resource' => 'route:admin/links/edit',
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
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/' . __NAMESPACE__ . '.lang',
            ),
        ),
    ),
    'Links' => array(
        'name' => 'Links',
        'note' => 'Manage categorized links to other sites',
        'can_be_disabled' => true,
        'client_usable' => true,
        'depends' => array('System', 'Category'),
        'stage' => 'development', 'version' => '1.0'
    ),
);