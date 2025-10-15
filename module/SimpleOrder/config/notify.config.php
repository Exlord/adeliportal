<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/12/14
 * Time: 1:36 PM
 */
return array(
    'SimpleOrder' => array(
        'simpleOrder_new_order_form' => array(
            'label' => 'simpleOrder_new_order_form',
            'description' => '',
            'notify_with' => array(
                'email' => 'simple-order/simple-order-admin/simple-order-email',
            ),
            'allow_user_role_override' => true, //default
            'allow_user_override' => false, //default
        ),
        'simpleOrder_new_stepOrder_form' => array(
            'label' => 'simpleOrder_new_stepOrder_form',
            'description' => '',
            'notify_with' => array(
                'email' => 'simple-order/simple-order-admin/simple-order-email',
            ),
            'allow_user_role_override' => true, //default
            'allow_user_override' => false, //default
        )
    )
);