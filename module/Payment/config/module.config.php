<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Payment;
return array(
    'service_manager' => array(
        'invokables' => array(
            'bank_info_table' => 'Payment\Model\BankInfoTable',
            'Payment_table' => 'Payment\Model\PaymentTable',
            'Payment_entity_table' => 'Payment\Model\PaymentEntityTable',
            'transactions_table' => 'Payment\Model\TransactionsTable',
            'user_amount_table' => 'Payment\Model\UserAmountTable',
            'payment_api' => 'Payment\API\Payment',
            'payment_entity_api' => 'Payment\API\Entity',
            'transactions_api' => 'Payment\API\Transactions',
            'payment_event_manager' => 'Payment\API\EventManager'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Payment\Controller\Payment' => 'Payment\Controller\PaymentController',
            'Payment\Controller\BankInfo' => 'Payment\Controller\BankInfoController',
            'Payment\Controller\Transactions' => 'Payment\Controller\TransactionsController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'payment' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/payment',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Payment\Controller\Payment',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'initialize' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/initialize',
                                    'defaults' => array(
                                        'controller' => 'Payment\Controller\Payment',
                                        'action' => 'initialize',
                                    ),
                                ),
                            ),
                            'validate' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/validate/:api',
                                    'defaults' => array(
                                        'controller' => 'Payment\Controller\Payment',
                                        'action' => 'validate',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'payment' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/payment',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Payment\Controller\Payment',
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
                                        'controller' => 'Payment\Controller\Payment',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                            'send-payment' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/send-payment',
                                    'defaults' => array(
                                        'controller' => 'Payment\Controller\Payment',
                                        'action' => 'send-payment',
                                    ),
                                ),
                            ),
                            'my-payments' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/my-payments',
                                    'defaults' => array(
                                        'controller' => 'Payment\Controller\Payment',
                                        'action' => 'my-payments',

                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array( /*'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'Payment\Controller\BankInfo',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),*/
                                ),
                            ),
                            'bank-info' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/bank-info',
                                    'defaults' => array(
                                        'controller' => 'Payment\Controller\BankInfo',
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
                                                'controller' => 'Payment\Controller\BankInfo',
                                                'action' => 'new',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'Payment\Controller\BankInfo',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'Payment\Controller\BankInfo',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'Payment\Controller\BankInfo',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'transactions' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/transactions',
                                    'defaults' => array(
                                        'controller' => 'Payment\Controller\Transactions',
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
                                                'controller' => 'Payment\Controller\Transactions',
                                                'action' => 'config',
                                            ),
                                        ),
                                    ),
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'Payment\Controller\Transactions',
                                                'action' => 'new',
                                            ),
                                        ),
                                    ),
                                    'validate-transactions' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/validate-transactions[/:params]',
                                            'constraints' => array(),
                                            'defaults' => array(
                                                'controller' => 'Payment\Controller\Transactions',
                                                'action' => 'validate-transactions',
                                            ),
                                        ),
                                    ),
                                    /*'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'Payment\Controller\Transactions',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'Payment\Controller\Transactions',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'Payment\Controller\Transactions',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),*/
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
            'reports' => array(
                'pages' => array(
                    'Payment' => array(
                        'label' => 'Bank Payments',
                        'route' => 'admin/payment/my-payments',
                        'resource' => 'route:admin/payment/my-payments',
                    ),
                    'Transactions' => array(
                        'label' => 'PAYMENT_FINANCIAL_TRANSACTIONS',
                        'route' => 'admin/payment/transactions',
                        'resource' => 'route:admin/payment/transactions',
                    ),
                )
            ),
            'structure' => array(
                'pages' => array(
                    array(
                        'label' => 'Bank Account Info',
                        'route' => 'admin/payment/bank-info',
                        'resource' => 'route:admin/payment/bank-info',
                    )
                )
            ),
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'PAYMENT_FINANCIAL_TRANSACTIONS',
                        'route' => 'admin/payment/transactions/config',
                        'resource' => 'route:admin/payment/transactions/config',
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
        'Payment\View\Helper\Widget' => 'PAYMENT_FINANCIAL_TRANSACTIONS'
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'Payment' => __DIR__ . '/../public',
            ),
        ),
    ),
    'Payment' => array(
        'name' => 'Payment Module',
        'note' => 'A Payment Module',
        'can_be_disabled' => true,
        'client_usable' => true,
        'depends' => array('System'),
        'stage' => 'development', 'version' => '1.0'
    ),
    'template_placeholders' => array(
        'Validate From Bank' => array(
            '__PAYERID__' => 'Payment Code',
            '__ORDERID__' => 'Order Code',
            '__TIME__' => 'exp : 15:30:25',
        )
    )
);