<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:08 AM
 */

namespace ProductShowcase\API;


use ProductShowcase\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'PS_PRODUCT_SHOWCASE',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_PRODUCT_SHOWCASE,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_PRODUCT_SHOWCASE_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit Section',
                            'note' => 'Edit Section',
                            'route' => Module::ADMIN_PRODUCT_SHOWCASE_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete Section',
                            'note' => 'Delete Section',
                            'route' => Module::ADMIN_PRODUCT_SHOWCASE_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update Section',
                            'note' => 'Update Section',
                            'route' => Module::ADMIN_PRODUCT_SHOWCASE_UPDATE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Orders Section',
                            'note' => '',
                            'route' => Module::ADMIN_PRODUCT_SHOWCASE_ORDERS,
                            'child_route' => array(
                                array(
                                    'label' => 'View All',
                                    'note' => 'View All Even if by Module not registered',
                                    'route' => Module::ADMIN_PRODUCT_SHOWCASE_ORDERS_ALL,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete Section',
                                    'note' => 'Delete Section',
                                    'route' => Module::ADMIN_PRODUCT_SHOWCASE_ORDERS_DEL,
                                    'child_route' => array(
                                        array(
                                            'label' => 'Delete All',
                                            'note' => 'Delete All Even if by Module not registered',
                                            'route' => Module::ADMIN_PRODUCT_SHOWCASE_ORDERS_DEL_ALL,
                                            'child_route' => ''
                                        )
                                    )
                                ),
                            )
                        ),
                    ),
                ),
            ),
        );


        return $dataItemAcl;
    }
} 