<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 9:59 AM
 */

namespace Notify\API;


class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Notifications',
                    'note' => 'view a list of notification',
                    'route' => 'route:admin/notify',
                    'child_route' => array(
                        array(
                            'label' => 'Delete',
                            'note' => 'delete your own notification messages',
                            'route' => 'route:admin/notify/delete',
                        ),
                    )
                ),
                array(
                    'label' => 'Configs',
                    'note' => 'notification configs (witch types of notifications should be send and with witch template)',
                    'route' => 'route:admin/notify/config',
                    'child_route' => array(
                        array(
                            'label' => 'Advance Configs',
                            'note' => 'notification configs customized for each user role',
                            'route' => 'route:admin/notify/config/advance',
                        ),
                    )
                ),
            )
        );
        return $dataItemAcl;
    }
} 