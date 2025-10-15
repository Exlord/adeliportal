<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:05 AM
 */

namespace Payment\API;


use Payment\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Payment',
            'note' => 'the payment form',
            'route' => Module::APP_PAYMENT,
        );
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Payment Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_PAYMENT,
                    'child_route' => array(

                        array(
                            'label' => 'My Payments',
                            'note' => 'My Payments',
                            'route' => Module::ADMIN_PAYMENT_MY_PAYMENT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Bank Info Section',
                            'note' => 'Bank Info Section',
                            'route' => Module::ADMIN_PAYMENT_BANK_INFO,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_PAYMENT_BANK_INFO_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_PAYMENT_BANK_INFO_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_PAYMENT_BANK_INFO_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_PAYMENT_BANK_INFO_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Transactions Section',
                            'route' => Module::ADMIN_PAYMENT_TRANSACTIONS,
                            'child_route' => array(
                                array(
                                    'label' => 'Configs',
                                    'route' => Module::ADMIN_PAYMENT_TRANSACTIONS_CONFIG,
                                ),
                                array(
                                    'label' => 'View All',
                                    'note' => 'View All Even if by Module not registered',
                                    'route' => Module::ADMIN_PAYMENT_TRANSACTIONS_VIEW_ALL,
                                ),
                                array(
                                    'label' => 'Deposit',
                                    'note' => 'Deposit For increase money to account',
                                    'route' => 'route:admin/payment/transactions/new',
                                    'child_route' => array(
                                        array(
                                            'label' => 'Direct Deposit',
                                            'note' => 'Direct Deposit For increase money to account, no payment required, admin only',
                                            'route' => Module::ADMIN_PAYMENT_TRANSACTIONS_NEW_DIRECT_DEPOSIT,
                                        ),
                                    )
                                ),
                            ),
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }
} 