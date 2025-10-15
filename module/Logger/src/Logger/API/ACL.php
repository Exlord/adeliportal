<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 9:50 AM
 */

namespace Logger\API;


class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'route' => 'route:admin/reports',
                    'child_route' => array(
                        array(
                            'label' => 'Logs',
                            'note' => 'System Logs (Errors ,Information ,Notice ...)',
                            'route' => '',
                        ),
                    )
                ),
            )
        );


        return $dataItemAcl;
    }
} 