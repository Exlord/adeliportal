<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:53 AM
 */

namespace Fields\API;


use Fields\Module;

class ACL {
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Fields Section',
                    'note' => 'Fields Section',
                    'route' => Module::ADMIN_FIELDS,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_FIELDS_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'Edit',
                            'route' => Module::ADMIN_FIELDS_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_FIELDS_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update',
                            'note' => 'Update',
                            'route' => Module::ADMIN_FIELDS_UPDATE,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        ) ;
        
            return $dataItemAcl;
    }
} 