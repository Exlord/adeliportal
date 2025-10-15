<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Mail;

use Mail\Model\MailArchiveTable;
use Mail\Model\MailTable;
use Mail\Model\SendTable;
use Zend\ServiceManager\ServiceLocatorInterface;

return array(
    'service_manager' => array(
        'factories' => array(
            'mail_db_adapter' => 'Mail\Factory\DbAdapter',
            'mail_queue_table' => 'Mail\Factory\MailQueueTable',
            'mail_archive_table' => 'Mail\Factory\MailArchiveTable',
            'mail_send_table' => 'Mail\Factory\MailSendTable',
        ),
        'invokables' => array(
            'mail_api' => 'Mail\API\Mail',

        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Mail\Controller\Mail' => 'Mail\Controller\MailController',
            'Mail\Controller\Cron' => 'Mail\Controller\CronController',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'mail' => array(
                    'options' => array(
                        'route' => '[<site>] mail',
                        'defaults' => array(
                            'controller' => 'Mail\Controller\Cron',
                            'action' => 'send'
                        )
                    )
                )
            )
        )
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'mail' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/mail',
                            'defaults' => array(
                                'controller' => 'Mail\Controller\Cron',
                                'action' => 'send',
                            ),
                        ),
                    ),
                    'quick-send-mail' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/quick-send-mail',
                            'defaults' => array(
                                'controller' => 'Mail\Controller\Mail',
                                'action' => 'quick-send-mail',
                            ),
                        ),
                    ),
                )
            ),
            'admin' => array(
                'child_routes' => array(
                    'configs' => array(
                        'child_routes' => array(
                            'mail' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/mail',
                                    'defaults' => array(
                                        'controller' => 'Mail\Controller\Mail',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                        )
                    ),
                    'mail' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/mail',
                            'defaults' => array(
                                'controller' => 'Mail\Controller\Mail',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Mail\Controller\Mail',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'archive' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/archive',
                                    'defaults' => array(
                                        'controller' => 'Mail\Controller\Mail',
                                        'action' => 'archive',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'Mail\Controller\Mail',
                                                'action' => 'archive-delete',
                                            ),
                                        ),
                                    ),
                                )
                            ),
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
                        'label' => 'Email',
                        'route' => 'admin/configs/mail',
                        'resource' => 'route:admin/configs/mail',
                    )
                ),
            ),
            'modules' => array(
                'pages' => array(
                    array(
                        'label' => 'Emails',
                        'route' => 'admin/mail',
                        'resource' => 'route:admin/mail',
                        'pages' => array(
                            array(
                                'label' => 'Archived Emails',
                                'route' => 'admin/mail/archive',
                                'resource' => 'route:admin/mail/archive',
                            ),
                        )
                    )
                )
            )
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'quick_send_mail' => 'Mail\View\Helper\QuickSendMail',
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
                'mail' => __DIR__ . '/../public',
            ),
        ),
    ),
);