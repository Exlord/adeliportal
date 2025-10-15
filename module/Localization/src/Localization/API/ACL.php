<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 9:46 AM
 */

namespace Localization\API;


use Localization\Module;

class ACL {
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Localization Config',
                    'route' => 'route:admin/localization-config',
                ),
                array(
                    'label' => 'Language Management',
                    'note' => '',
                    'route' => Module::ADMIN_LANGUAGE,
                    'child_route' => array(
                        array(
                            'label' => 'Update',
                            'note' => '',
                            'route' => Module::ADMIN_LANGUAGE_UPDATE,
                        ),
                    )
                ),
                array(
                    'label' => 'Content Translation',
                    'route' => 'route:admin/translation',
                    'child_route' => array(
                        array(
                            'label' => 'Miscellaneous Content Translation',
                            'route' => 'route:admin/translate-miscellaneous',
                            'child_route' => array(
                                array(
                                    'label' => 'Add',
                                    'route' => 'route:admin/translate-miscellaneous/add',
                                ),
                                array(
                                    'label' => 'Edit',
                                    'route' => 'route:admin/translate-miscellaneous/edit',
                                ),
                            )
                        ),
                    )
                ),
            ),
        );
        
            return $dataItemAcl;
    }
} 