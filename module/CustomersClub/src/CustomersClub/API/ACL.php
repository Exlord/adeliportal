<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:49 AM
 */

namespace CustomersClub\API;


use CustomersClub\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Customers Club',
                    'note' => '',
                    'route' => 'route:admin/customers-club',
                    'child_route' => array(
                        array(
                            'label' => 'Config',
                            'note' => '',
                            'route' => 'route:admin/customers-club/config',
                            'child_route' => array()
                        ),
                        array(
                            'label' => 'My Points',
                            'note' => '',
                            'route' => 'route:admin/customers-club/my-points',
                            'child_route' => array()
                        ),
                        array(
                            'label' => 'Points',
                            'note' => '',
                            'route' => 'route:admin/customers-club/points',
                            'child_route' => array(
                                array(
                                    'label' => 'New',
                                    'note' => '',
                                    'route' => Module::NEW_POINT,
                                    'child_route' => array()
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => '',
                                    'route' => 'route:admin/customers-club/points/edit',
                                    'child_route' => array()
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => '',
                                    'route' => 'route:admin/customers-club/points/delete',
                                    'child_route' => array()
                                )
                            )
                        ),
                        array(
                            'label' => 'View Customer Records',
                            'note' => '',
                            'route' => Module::VIEW_CUSTOMER_RECORDS,
                            'child_route' => array()
                        ),
                    )
                )
            )
        );

        return $dataItemAcl;
    }
} 