<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Category;
return array(
    'service_manager' => array(
        'invokables' => array(
            'category_table' => 'Category\Model\CategoryTable',
            'category_item_table' => 'Category\Model\CategoryItemTable',
            'entity_relation_table' => 'Category\Model\EntityRelationTable',
            'category_item_api' => 'Category\API\CategoryItem',
            'category_item_list_api' => 'Category\API\CategoryList',
            'category_event_manager' => 'Category\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Category\Controller\Category' => 'Category\Controller\CategoryController',
            'Category\Controller\Items' => 'Category\Controller\ItemsController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'category-list' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/categories/:catId[/:catItemId]',
                            'defaults' => array(
                                'controller' => 'Category\Controller\Items',
                                'action' => 'cat-item-list-view-block',
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'category-list' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/category[/:page]',
                            'defaults' => array(
                                'controller' => 'Category\Controller\Category',
                                'action' => 'index',
                                'page' => 1
                            ),
                            'constraints' => array(
                                'page' => '[0-9]+',
                            ),
                        ),
                    ),
                    'category' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/category',
                            'defaults' => array(
                                'controller' => 'Category\Controller\Category',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'sub-category' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/sub-category',
                                    'defaults' => array(
                                        'controller' => 'Category\Controller\Items',
                                        'action' => 'sub-category',
                                    ),
                                ),
                            ),
                            'items-list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:catId/items[/:parentId[/:page]]',
                                    'constraints' => array(
                                        'catId' => '[0-9]+',
                                        'page' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Category\Controller\Items',
                                        'action' => 'index',
                                        'page' => 1
                                    ),
                                ),
                            ),
                            'get-item-list' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/item-list',
                                    'defaults' => array(
                                        'controller' => 'Category\Controller\Items',
                                        'action' => 'get-item-list',
                                    ),
                                ),
                            ),
                            'get-category-list' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/category-list',
                                    'defaults' => array(
                                        'controller' => 'Category\Controller\Category',
                                        'action' => 'get-category-list',
                                    ),
                                ),
                            ),
                            'items' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:catId/items[/:parentId]',
                                    'constraints' => array(
                                        'catId' => '[0-9]+',
                                        'parentId' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Category\Controller\Items',
                                        'action' => 'index',
                                        'parentId' => 0,
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'Category\Controller\Items',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'Category\Controller\Items',
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
                                                'controller' => 'Category\Controller\Items',
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
                                                'controller' => 'Category\Controller\Items',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                    'search' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/search',
                                            'constraints' => array(),
                                            'defaults' => array(
                                                'controller' => 'Category\Controller\Items',
                                                'action' => 'search',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'Category\Controller\Category',
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
                                        'controller' => 'Category\Controller\Category',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/delete',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'Category\Controller\Category',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                        )
                    )
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'structure' => array(
                'pages' => array(
                    array(
                        'label' => 'Categories',
                        'route' => 'admin/category',
                        'resource' => 'route:admin/category',
                        'pages' => array(
                            array(
                                'label' => 'New Category',
                                'route' => 'admin/category/new',
                                'resource' => 'route:admin/category/new',
                            ),
                            array(
                                'label' => 'Edit Category',
                                'route' => 'admin/category/edit',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/category/delete',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/category/items',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/category/items/new',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/category/items/edit',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/category/items/delete',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/category/items-list',
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
            'category_list_block' => 'Category\View\Helper\CategoryListBlock',
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
    'widgets' => array(
        'Category\View\Helper\Widget' => 'category_widget_config_label'
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'category' => __DIR__ . '/../public',
            ),
        ),
    ),
    'components' => array(
        'category_list_block' => array(
            'label' => 'Category List',
            'description' => 'Show Category List',
            'helper' => 'category_list_block',
        ),
    )
);