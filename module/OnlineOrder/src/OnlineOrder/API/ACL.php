<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:01 AM
 */

namespace OnlineOrder\API;


use OnlineOrder\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Online Order Section',
            'note' => 'Online Order Section',
            'route' => Module::APP_ONLINE_ORDER,
            'child_route' => array(
                array(
                    'label' => 'Create',
                    'note' => 'Possible order for Website visitors',
                    'route' => Module::APP_ONLINE_ORDER_NEW,
                    'child_route' => ''
                ),
                /* array(
                     'label' => 'Check Domains',
                     'note' => 'Check Domains',
                     'route' => Module::APP_ONLINE_ORDER_CHECK_DOMAIN,
                     'child_route' => ''
                 ),
                 array(
                     'label' => 'Access client area online order view factor',
                     'note' => 'Access client area online order view factor',
                     'route' => Module::APP_ONLINE_ORDER_VIEW_FACTOR,
                     'child_route' => ''
                 ),*/
            ),
        );

        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Online Order Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_ONLINE_ORDER,
                    'child_route' => array(
                        array(
                            'label' => 'Orders',
                            'note' => 'Orders',
                            'route' => Module::ADMIN_ONLINE_ORDER_ORDERS,
                            'child_route' => array(
                                array(
                                    'label' => 'View All',
                                    'note' => 'View All Even if by Module not registered',
                                    'route' => Module::ADMIN_ONLINE_ORDER_ORDERS_ALL,
                                    'child_route' => ''
                                ),
                                /* array(
                                     'label' => 'Create',
                                     'note' => 'Create',
                                     'route' => Module::ADMIN_ONLINE_ORDER_ORDERS_NEW,
                                     'child_route' => ''
                                 ),*/
                                array(
                                    'label' => 'Extension',
                                    'note' => '',
                                    'route' => Module::ADMIN_ONLINE_ORDER_ORDERS_EXTENSION,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_ONLINE_ORDER_ORDERS_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_ONLINE_ORDER_ORDERS_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_ONLINE_ORDER_ORDERS_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Configs',
                            'note' => 'Configs',
                            'route' => Module::ADMIN_ONLINE_ORDER_CONFIG,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'View Widget Info',
                            'note' => '',
                            'route' => Module::ADMIN_ONLINE_ORDER_WIDGET,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Sub Domains Section',
                            'note' => '',
                            'route' => Module::ADMIN_ONLINE_ORDER_SUB_DOMAINS,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }
} 