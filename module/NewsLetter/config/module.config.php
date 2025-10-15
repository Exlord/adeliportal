<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace NewsLetter;
return array(
    'service_manager' => array(
        'invokables' => array(
            'news_letter_template' => 'NewsLetter\Model\NewsLetterTemplateTable',
            'news_letter_api' => 'NewsLetter\API\NewsLetter',
            'news_letter_sign_up_table' => 'NewsLetter\Model\NewsletterSignUpTable',
            'news_letter_event_manager' => 'NewsLetter\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'NewsLetter\Controller\Admin' => 'NewsLetter\Controller\AdminController',
            'NewsLetter\Controller\Client' => 'NewsLetter\Controller\ClientController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'news-letter' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/news-letter',
                            'defaults' => array(
                                'controller' => 'NewsLetter\Controller\Admin',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'send' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/send',
                                    'defaults' => array(
                                        'controller' => 'NewsLetter\Controller\Admin',
                                        'action' => 'send-news-letter',
                                    ),
                                ),
                            ),
                            'emails-list' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/emails-list',
                                    'defaults' => array(
                                        'controller' => 'NewsLetter\Controller\Admin',
                                        'action' => 'emails-list',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'NewsLetter\Controller\Admin',
                                                'action' => 'emails-delete',
                                            ),
                                        ),
                                    ),
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'NewsLetter\Controller\Admin',
                                                'action' => 'emails-update',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'NewsLetter\Controller\Admin',
                                        'action' => 'config',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'more' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/more/:api/:namespace',
                                            'defaults' => array(
                                                'controller' => 'NewsLetter\Controller\Admin',
                                                'action' => 'config-more',
                                            ),
                                        ),
                                    ),
                                    'global' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/global',
                                            'defaults' => array(
                                                'controller' => 'NewsLetter\Controller\Admin',
                                                'action' => 'global-config',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'template' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/template',
                                    'defaults' => array(
                                        'controller' => 'NewsLetter\Controller\Admin',
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
                                                'controller' => 'NewsLetter\Controller\Admin',
                                                'action' => 'new',

                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'NewsLetter\Controller\Admin',
                                                'action' => 'edit',

                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'NewsLetter\Controller\Admin',
                                                'action' => 'delete',

                                            ),
                                        ),
                                    ),
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'NewsLetter\Controller\Admin',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'app' => array(
                'child_routes' => array(
                    'newsletter-sign-up' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/newsletter-sign-up',
                            'defaults' => array(
                                'controller' => 'NewsLetter\Controller\Client',
                                'action' => 'sign-up',
                            ),
                        ),
                    ),
                )
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'structure' => array(
                'pages' => array(
                    array(
                        'label' => 'News Letter',
                        'route' => 'admin/news-letter',
                        'resource' => 'route:admin/news-letter',
                        'pages' => array(
                            array(
                                'label' => 'Send News Letter',
                                'route' => 'admin/news-letter/send',
                                'resource' => 'route:admin/news-letter/send',
                            ),
                            array(
                                'label' => 'Emails',
                                'route' => 'admin/news-letter/emails-list',
                                'resource' => 'route:admin/news-letter/emails-list',
                            ),
                        )
                    )
                ),
            ),
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Newsletter',
                        'route' => 'admin/news-letter/config',
                        'resource' => 'route:admin/news-letter/config',
                        'pages' => array(
                            array(
                                'label' => 'Base Configs',
                                'route' => 'admin/news-letter/config',
                                'resource' => 'route:admin/news-letter/config',
                            ),
                            array(
                                'label' => 'Advanced Configs',
                                'route' => 'admin/news-letter/config/global',
                                'resource' => 'route:admin/news-letter/config/global',
                            ),
                        )
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
    'view_helpers' => array(
        'invokables' => array(
            'newsletterSignUp' => 'NewsLetter\View\Helper\NewsletterSignUp',
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
                'NewsLetter' => __DIR__ . '/../public',
            ),
        ),
    ),
    'NewsLetter' => array(
        'name' => 'NewsLetter Module',
        'note' => 'A NewsLetter Module',
        'can_be_disabled' => true,
        'client_usable' => true,
        'depends' => array('System', 'Mail'),
        'stage' => 'development', 'version' => '1.0'
    ),
);