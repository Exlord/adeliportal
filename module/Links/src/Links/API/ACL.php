<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 9:45 AM
 */

namespace Links\API;


use Links\Module;

class ACL {
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Links Section',
            'note' => 'Links Section',
            'route' => 'route:app/links',
            'child_route' => array(
                array(
                    'label' => 'Links Category',
                    'note' => 'Links Category',
                    'route' => 'route:app/links/category',
                    'child_route' => ''
                ),
            ),
        );

        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Links List',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_LINKS_LIST,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Links Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_LINKS,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_LINKS_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'Edit',
                            'route' => Module::ADMIN_LINKS_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_LINKS_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update',
                            'note' => 'Update',
                            'route' => Module::ADMIN_LINKS_UPDATE,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );


            return $dataItemAcl;
    }
} 