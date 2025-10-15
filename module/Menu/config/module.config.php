<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Menu;
return array(
    'service_manager' => array(
        'invokables' => array(
            // Keys are the service names
            // Values are valid class names to instantiate.
            'menu_table' => 'Menu\Model\MenuTable',
            'menu_item_table' => 'Menu\Model\MenuItemTable',
            'menu_api' => 'Menu\API\Menu',
            'menu_event_manager' => 'Menu\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Menu\Controller\Menu' => 'Menu\Controller\MenuController',
            'Menu\Controller\MenuItem' => 'Menu\Controller\MenuItemController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'menu' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/menu',
                            'defaults' => array(
                                'controller' => 'Menu\Controller\Menu',
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
                                        'controller' => 'Menu\Controller\Menu',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'Menu\Controller\Menu',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Menu\Controller\Menu',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'items' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:menuId/items[/:parentId]',
                                    'defaults' => array(
                                        'controller' => 'Menu\Controller\MenuItem',
                                        'action' => 'index',
                                        'parentId' => 0
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'Menu\Controller\MenuItem',
                                                'action' => 'new',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'Menu\Controller\MenuItem',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'Menu\Controller\MenuItem',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                    'delete-img' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete-img',
                                            'defaults' => array(
                                                'controller' => 'Menu\Controller\MenuItem',
                                                'action' => 'delete-img',
                                            ),
                                        ),
                                    ),
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
            'structure' => array(
                'pages' => array(
                    array(
                        'label' => 'Menu',
                        'route' => 'admin/menu',
                        'resource' => 'route:admin/menu',
                        'pages' => array(
                            array(
                                'label' => 'New Menu',
                                'route' => 'admin/menu/new',
                                'resource' => 'route:admin/menu/new',
                            ),
                            array(
                                'label' => 'Edit Menu',
                                'route' => 'admin/menu/edit',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Delete Menu',
                                'route' => 'admin/menu/delete',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Menu Items',
                                'route' => 'admin/menu/items',
                                'visible' => false,
                                'pages' => array(
                                    array(
                                        'label' => 'New Menu Item',
                                        'route' => 'admin/menu/items/new',
                                        'visible' => false
                                    ),
                                    array(
                                        'label' => 'Edit Menu Item',
                                        'route' => 'admin/menu/items/edit',
                                        'visible' => false
                                    ),
                                    array(
                                        'label' => 'Delete Menu Item',
                                        'route' => 'admin/menu/items/delete',
                                        'visible' => false
                                    ),
                                )
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
            'menu_block' => 'Menu\View\Helper\MenuBlock'
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
        'Menu\View\Helper\Widget' => 'menu_widget_config_label'
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'menu' => __DIR__ . '/../public',
            ),
        ),
    ),
    'components' => array(
        'menu_block' => array(
            'label' => 'Menu',
            'description' => '',
            'helper' => 'menu_block',
        )
    ),
);