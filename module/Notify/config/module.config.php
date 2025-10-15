<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Notify;
return array(
    'service_manager' => array(
        'invokables' => array(
            'notify_table' => 'Notify\Model\NotifyTable',
            'notify_api' => 'Notify\API\Notify'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Notify\Controller\Notify' => 'Notify\Controller\NotifyController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'notify' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/notifications',
                            'defaults' => array(
                                'controller' => 'Notify\Controller\Notify',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'read' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/read',
                                    'defaults' => array(
                                        'controller' => 'Notify\Controller\Notify',
                                        'action' => 'read',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Notify\Controller\Notify',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'Notify\Controller\Notify',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'Notify\Controller\Notify',
                                        'action' => 'config',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'advance' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/advance',
                                            'defaults' => array(
                                                'controller' => 'Notify\Controller\Notify',
                                                'action' => 'advance-config',
                                            ),
                                        ),
                                    )
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
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Notification',
                        'route' => 'admin/notify/config',
                        'resource' => 'route:admin/notify/config',
                        'pages' => array(
                            array(
                                'label' => 'Advance',
                                'route' => 'admin/notify/config/advance',
                                'resource' => 'route:admin/notify/config/advance',
                            )
                        )
                    )
                )
            ),
            'modules' => array(
                'pages' => array(
                    array(
                        'label' => 'Notifications',
                        'route' => 'admin/notify',
                        'resource' => 'route:admin/notify',
                        'pages' => array(
                            array(
                                'label' => 'Delete',
                                'route' => 'admin/notify/delete',
                                'resource' => 'route:admin/notify/delete',
                                'visible' => false
                            )
                        )
                    )
                )
            ),
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'notifications' => 'Notify\View\Helper\Notifications',
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
                'Notify' => __DIR__ . '/../public',
            ),
        ),
    ),
);