<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:56 AM
 */

namespace FormsManager\API;


use FormsManager\Module;

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
                            'label' => 'Forms',
                            'note' => 'Forms',
                            'route' => Module::ADMIN_FORMS_CONFIGS_FORMS,
                            'child_route' => ''
                        ),
                    ),
                ),
                array(
                    'label' => 'Forms Data Section',
                    'note' => 'Forms Data Section',
                    'route' => Module::ADMIN_FORMS_DATA,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_FORMS_DATA_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit Section',
                            'note' => 'Edit Section',
                            'route' => Module::ADMIN_FORMS_DATA_EDIT,
                            'child_route' => array(
                                array(
                                    'label' => 'Edit All',
                                    'note' => 'Edit All Even if by Module not registered',
                                    'route' => Module::ADMIN_FORMS_DATA_EDIT_ALL,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Delete Section',
                            'note' => 'Delete Section',
                            'route' => Module::ADMIN_FORMS_DATA_DELETE,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete All',
                                    'note' => 'Delete All Even if by Module not registered',
                                    'route' => Module::ADMIN_FORMS_DATA_DELETE_ALL,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'View',
                            'note' => 'View',
                            'route' => Module::ADMIN_FORMS_DATA_VIEW,
                            'child_route' => ''
                        ),
                    ),
                ),
                array(
                    'label' => 'Forms Section',
                    'note' => 'Forms Section',
                    'route' => Module::ADMIN_FORMS,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_FORMS_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'Edit',
                            'route' => Module::ADMIN_FORMS_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_FORMS_DELETE,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );


        return $dataItemAcl;
    }
} 