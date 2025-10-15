<?php
namespace ContentSharing;
return array(
    'service_manager' => array(
    ),
    'controllers' => array(
        'invokables' => array(
            'ContentSharing\Controller\ContentSharing' => 'ContentSharing\Controller\ContentSharingController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'content-sharing' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/content-sharing',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'ContentSharing\Controller\ContentSharing',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'ContentSharing\Controller\ContentSharing',
                                        'action' => 'config',

                                    ),
                                ),
                            ),
                        ),
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
                        'label' => 'Content Sharing',
                        'route' => 'admin/content-sharing/config',
                        'resource' => 'route:admin/content-sharing/config',
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
            'add_to_any_helper' => 'ContentSharing\View\Helper\AddToAny',
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
                'content-sharing' => __DIR__ . '/../public',
            ),
        ),
    ),
);