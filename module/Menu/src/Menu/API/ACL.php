<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 9:53 AM
 */

namespace Menu\API;


use Menu\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Menu Section',
                    'note' => 'Menu Section',
                    'route' => Module::ADMIN_MENU,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_MENU_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'Edit',
                            'route' => Module::ADMIN_MENU_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_MENU_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Menu Items Section',
                            'note' => 'Menu Items Section',
                            'route' => Module::ADMIN_MENU_ITEMS,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_MENU_ITEMS_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_MENU_ITEMS_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_MENU_ITEMS_DELETE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }
} 