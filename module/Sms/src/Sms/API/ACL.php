<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:16 AM
 */

namespace Sms\API;


use Sms\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Sms Section',
                    'note' => 'Sms Section',
                    'route' => Module::ADMIN_SMS,
                    'child_route' => array(
                        array(
                            'label' => 'Send Sms',
                            'note' => 'Send Sms',
                            'route' => Module::ADMIN_SMS_SEND,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Configs',
                            'note' => 'Configs',
                            'route' => Module::ADMIN_SMS_CONFIG,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }
} 