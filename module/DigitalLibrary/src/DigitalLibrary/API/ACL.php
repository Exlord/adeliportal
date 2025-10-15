<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:11 AM
 */

namespace DigitalLibrary\API;


class ACL
{
    public static function load()
    {
        $acl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Book',
                    'note' => 'digital library books',
                    'route' => 'route:admin/book',
                    'child_route' => array(
                        array(
                            'label' => 'New',
                            'note' => '',
                            'route' => 'route:admin/book/new',
                            'child_route' => array()
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => '',
                            'route' => 'route:admin/book/edit',
                            'child_route' => array()
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => '',
                            'route' => 'route:admin/book/delete',
                            'child_route' => array()
                        )
                    )
                )
            )
        );


        return $acl;
    }
} 