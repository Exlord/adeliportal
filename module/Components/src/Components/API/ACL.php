<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:46 AM
 */

namespace Components\API;


use Components\Module;

class ACL
{
    public function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Block Section',
                    'note' => 'Block Section',
                    'route' => Module::ADMIN_BLOCK,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_BLOCK_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'Edit',
                            'route' => Module::ADMIN_BLOCK_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_BLOCK_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update',
                            'note' => 'Update',
                            'route' => Module::ADMIN_BLOCK_UPDATE,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );


        return $dataItemAcl;
    }
} 