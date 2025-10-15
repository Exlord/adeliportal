<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace PM;
return array(
    'service_manager' => array(
        'invokables' => array(
            'pm_table' => 'PM\Model\PMTable',
            'pm_event_manager' => 'PM\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'PM\Controller\PM' => 'PM\Controller\PM',
            'PM\Controller\PMAdmin' => 'PM\Controller\PMAdmin',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'pm' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/pm',
                            'defaults' => array(
                                'controller' => 'PM\Controller\PMAdmin',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'new' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/new[/:to]',
                                    'defaults' => array(
                                        'controller' => 'PM\Controller\PM',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'reply' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/reply[/:to]',
                                    'defaults' => array(
                                        'controller' => 'PM\Controller\PM',
                                        'action' => 'reply',
                                    ),
                                ),
                            ),
                            'view' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/view',
                                    'defaults' => array(
                                        'controller' => 'PM\Controller\PMAdmin',
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'PM\Controller\PMAdmin',
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
            'modules' => array(
                'pages' => array(
                    array(
                        'label' => 'PM',
                        'route' => 'admin/pm',
                        'resource' => 'route:admin/pm',
                        'pages' => array(
                            array(
                                'label' => 'Send',
                                'route' => 'admin/pm/new',
                                'resource' => 'route:admin/pm/new',
                            ),
                            array(
                                'label' => 'View',
                                'route' => 'admin/pm/view',
                                'resource' => 'route:admin/pm/view',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/PM/delete',
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
            'pm' => 'PM\View\Helper\PM',
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
                'pm' => __DIR__ . '/../public',
            ),
        ),
    ),
);