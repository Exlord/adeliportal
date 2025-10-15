<?php
namespace Page;
return array(
    'service_manager' => array(
        'invokables' => array(
            'page_table' => 'Page\Model\PageTable',
            'page_tags_table' => 'Page\Model\PageTagsTable',
            'page_api' => 'Page\API\Page',
            'page_event_manager' => 'Page\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Page\Controller\Page' => 'Page\Controller\PageController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'page-view' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/page/:id/:title',
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'view',
                                'type' => 'page'
                            ),
                        ),
                    ),
                    'content' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/tags[/:tagId/:tagName]',
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'view-content',
                            ),
                        ),
                    ),
                    'single-content' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/content[/:id[/:title]]',
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'view',
                                'type' => 'content'
                            ),
                        ),
                    ),
                )
            ),
            'admin' => array(
                'child_routes' => array(
                    'menu-page-list' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/menu-page-list/:type',
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'menu-page-list',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'menu-page-tag-list' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/menu-page-tag-list',
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'menu-page-tag-list',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'page-config' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/page-config',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'config',
                            ),
                        ),
                    ),
                    'page-list' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/page[/:page]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'index',
                                'page' => 1
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'page' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/page',
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'index',
                                'type' => 'page'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'update' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'Page\Controller\Page',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'Page\Controller\Page',
                                        'action' => 'new',
                                        'type' => 1,
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
                                        'controller' => 'Page\Controller\Page',
                                        'action' => 'edit',
                                        'type' => 1,
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'Page\Controller\Page',
                                        'action' => 'delete',
                                        'type' => 1,
                                    ),
                                ),
                            ),
                            'delete-img' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete-img',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'Page\Controller\Page',
                                        'action' => 'delete-img',
                                    ),
                                ),
                            ),

                        )
                    ),
                    'content' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/content',
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'index',
                                'type' => 'content'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'update' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'Page\Controller\Page',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'Page\Controller\Page',
                                        'action' => 'new',
                                        'type' => 0,
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
                                        'controller' => 'Page\Controller\Page',
                                        'action' => 'edit',
                                        'type' => 0,
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'Page\Controller\Page',
                                        'action' => 'delete',
                                        'type' => 0,
                                    ),
                                ),
                            ),
                        )
                    ),
                    'auto-complete' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/auto-complete',
                            'defaults' => array(
                                'controller' => 'Page\Controller\Page',
                                'action' => 'auto-complete',
                            ),
                        ),
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
                        'label' => 'Constant Pages',
                        'route' => 'admin/page',
                        'resource' => 'route:admin/page',
                        'pages' => array(
                            array(
                                'route' => 'admin/page/new',
                                'label' => 'New',
                                'resource' => 'route:admin/page/new',
                            ),
                            array(
                                'route' => 'admin/page/edit',
                                'label' => 'Edit',
                                'resource' => 'route:admin/page/edit',
                                'visible' => false
                            ),
                        )
                    ),
                    array(
                        'label' => 'Content',
                        'route' => 'admin/content',
                        'resource' => 'route:admin/content',
                        'pages' => array(
                            array(
                                'route' => 'admin/content/new',
                                'label' => 'New',
                                'resource' => 'route:admin/content/new',
                            ),
                            array(
                                'route' => 'admin/content/edit',
                                'label' => 'Edit',
                                'resource' => 'route:admin/content/edit',
                                'visible' => false
                            ),
                        )
                    )
                ),
            ),
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Pages',
                        'route' => 'admin/page-config',
                        'resource' => 'route:admin/page-config',
                    ),
                )
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
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'Page' => __DIR__ . '/../public',
            ),
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'content_block' => 'Page\View\Helper\ContentBlock',
        ),
    ),
    'components' => array(
        'content_block' => array(
            'label' => 'Content',
            'description' => 'Content in the selected category',
            'helper' => 'content_block',
        ),
    ),
    'widgets' => array(
        'Page\View\Helper\Widget' => 'page_widget_config_label'
    ),
    'news_letter' => array(
        '\Page\API\Page' => 'Content',
    ),

);