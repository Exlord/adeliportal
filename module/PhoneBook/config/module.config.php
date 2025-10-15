<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace PhoneBook;
return array(
    'service_manager' => array(
        'invokables' => array(
            'phoneBook_table' => 'PhoneBook\Model\PhoneBookTable',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'PhoneBook\Controller\PhoneBook' => 'PhoneBook\Controller\PhoneBookController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'phone-book' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/phone-book',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'PhoneBook\Controller\PhoneBook',
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
                                        'controller' => 'PhoneBook\Controller\PhoneBook',
                                        'action' => 'new',

                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'PhoneBook\Controller\PhoneBook',
                                        'action' => 'edit',

                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'PhoneBook\Controller\PhoneBook',
                                        'action' => 'delete',

                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'PhoneBook\Controller\PhoneBook',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'word-export' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/word-export',
                                    'defaults' => array(
                                        'controller' => 'PhoneBook\Controller\PhoneBook',
                                        'action' => 'word-export',
                                    ),
                                ),
                            ),
                            'print' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/print[/:data]',
                                    'defaults' => array(
                                        'controller' => 'PhoneBook\Controller\PhoneBook',
                                        'action' => 'print',
                                    ),
                                ),
                            ),
                            'prepare-print' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/prepare-print',
                                    'defaults' => array(
                                        'controller' => 'PhoneBook\Controller\PhoneBook',
                                        'action' => 'prepare-print',
                                    ),
                                ),
                            ),
                            'send-phone-book-sms' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/send-phone-book-sms',
                                    'defaults' => array(
                                        'controller' => 'PhoneBook\Controller\PhoneBook',
                                        'action' => 'send-phone-book-sms',
                                    ),
                                ),
                            ),
                            'send-phone-book-email' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/send-phone-book-email',
                                    'defaults' => array(
                                        'controller' => 'PhoneBook\Controller\PhoneBook',
                                        'action' => 'send-phone-book-email',
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
            'contents' => array(
                'pages' => array(
                    array(
                        'label' => 'Phone Book',
                        'route' => 'admin/phone-book',
                        'resource' => 'route:admin/phone-book',
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
                'PhoneBook' => __DIR__ . '/../public',
            ),
        ),
    ),
    'PhoneBook' => array(
        'name' => 'PhoneBook Module',
        'note' => 'A PhoneBook Module',
        'can_be_disabled' => true,
        'client_usable' => true,
        'depends' => array('System'),
        'stage' => 'development','version'=>'1.0'
    ),
);