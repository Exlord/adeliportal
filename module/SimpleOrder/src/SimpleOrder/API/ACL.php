<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:14 AM
 */

namespace SimpleOrder\API;


class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'route' => 'route:admin/configs',
                    'child_route' => array(
                        array(
                            'label' => 'Simple Order',
                            'note' => '',
                            'route' => 'route:admin/simple-order/config',
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }
} 