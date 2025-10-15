<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Components;
return array(
    'service_manager' => array(
        'invokables' => array(
            // Keys are the service names
            // Values are valid class names to instantiate.
            'block_table' => 'Components\Model\BlockTable',
            'block_url_table' => 'Components\Model\UrlTable',
            'block_api' => 'Components\API\Block',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Components\Controller\BlockManager' => 'Components\Controller\BlockManagerController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'may_terminate' => true,
                'child_routes' => array()
            ),
            'admin' => array(
                'child_routes' => array(
                    'block' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/block',
                            'defaults' => array(
                                'controller' => 'Components\Controller\BlockManager',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'new' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/new[/:type]',
                                    'defaults' => array(
                                        'controller' => 'Components\Controller\BlockManager',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Components\Controller\BlockManager',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'Components\Controller\BlockManager',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'Components\Controller\BlockManager',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                        )
                    ),
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
            'blocks' => 'Components\View\Helper\Blocks',
        ),
    ),
    'navigation' => array(
        'admin_menu' => array(
            'structure' => array(
                'pages' => array(
                    array(
                        'label' => 'Blocks',
                        'route' => 'admin/block',
                        'resource' => 'route:admin/block',
                        'pages' => array(
                            array(
                                'label' => 'New Block',
                                'route' => 'admin/block/new',
                                'resource' => 'route:admin/block/new',
                            ),
                            array(
                                'label' => 'Edit Block',
                                'route' => 'admin/block/edit',
                                'resource' => 'route:admin/block/edit',
                                'visible' => false
                            )
                        ),
                    )
                ),
            ),
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
);