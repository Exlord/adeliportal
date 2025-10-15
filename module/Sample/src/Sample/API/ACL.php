<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:11 AM
 */

namespace Sample\API;


class ACL
{
    public static function load()
    {
        $acl = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Sample',
                    'note' => '',
                    'route' => 'route:admin/sample',
                    'child_route' => array()
                )
            )
        );

        return $acl;
    }
} 