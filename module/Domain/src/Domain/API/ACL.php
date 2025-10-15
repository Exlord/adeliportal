<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:51 AM
 */

namespace Domain\API;


class ACL {
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Domain',
                    'note' => 'create ,edit and delete domains for the system',
                    'route' => 'route:admin/domain',
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'route' => 'route:admin/domain/new',
                        ),
                        array(
                            'label' => 'Edit',
                            'route' => 'route:admin/domain/edit',
                        ),
                        array(
                            'label' => 'Delete',
                            'route' => 'route:admin/domain/delete'
                        ),
                    )
                )
            )
        );

            return $dataItemAcl;
    }
} 