<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:02 AM
 */

namespace OnlineOrders\API;


class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Online Orders',
                    'route' => 'route:app/online-orders',
                    'child_route' => array(),
                ),
            ),
        );

        return $dataItemAcl;
    }
} 