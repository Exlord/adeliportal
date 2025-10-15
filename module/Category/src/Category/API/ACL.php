<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:44 AM
 */

namespace Category\API;


use Category\Module;

class ACL {
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Category List',
                    'note' => 'Category List',
                    'route' => Module::ADMIN_CATEGORY_LIST,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Category Section',
                    'note' => 'Category Section',
                    'route' => Module::ADMIN_CATEGORY,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_CATEGORY_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'Edit',
                            'route' => Module::ADMIN_CATEGORY_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_CATEGORY_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Items',
                            'note' => 'Items List',
                            'route' => Module::ADMIN_CATEGORY_ITEMS_LIST,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Get Item List',
                            'note' => '',
                            'route' => Module::ADMIN_CATEGORY_ITEMS_GET_ITEM_LIST,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Get Category List',
                            'note' => '',
                            'route' => Module::ADMIN_CATEGORY_ITEMS_GET_CATEGORY_LIST,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Items Section',
                            'note' => 'Items Section',
                            'route' => Module::ADMIN_CATEGORY_ITEMS,
                            'child_route' => array(
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_CATEGORY_ITEMS_UPDATE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_CATEGORY_ITEMS_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_CATEGORY_ITEMS_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_CATEGORY_ITEMS_DELETE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

            return $dataItemAcl;
    }
} 