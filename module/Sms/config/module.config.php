<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Sms;
return array(
    'service_manager' => array(
        'invokables' => array(
            'sms_api' => 'Sms\API\SMS',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Sms\Controller\Sms' => 'Sms\Controller\SmsController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'sms' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/sms',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Sms\Controller\Sms',
                                'action' => 'send-sms',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'send-sms' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/send-sms',
                                    'defaults' => array(
                                        'controller' => 'Sms\Controller\Sms',
                                        'action' => 'send-sms',

                                    ),
                                ),
                            ),
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'Sms\Controller\Sms',
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
            'modules' => array(
                'pages' => array(
                    'Sms' => array(
                        'label' => 'Sms',
                        'route' => 'admin/sms/send-sms',
                        'resource' => 'route:admin/sms/send-sms',
                        'uri' => '#sms',
                    ),
                )
            ),
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Sms',
                        'route' => 'admin/sms/config',
                        'resource' => 'route:admin/sms/config',
                    )
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
                'Sms' => __DIR__ . '/../public',
            ),
        ),
    ),
    'Sms' => array(
        'name' => 'Sms Module',
        'note' => 'A Sms Module',
        'can_be_disabled' => true,
        'client_usable' => true,
        'depends' => array('System'),
        'stage' => 'development', 'version' => '1.0'
    ),
);