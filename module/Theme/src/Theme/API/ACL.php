<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:18 AM
 */

namespace Theme\API;


class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Themes',
                    'note' => 'view a list of available system themes',
                    'route' => 'route:admin/themes',
                    'child_route' => array(
                        array(
                            'label' => 'Change Theme',
                            'note' => 'Change client and admin themes',
                            'route' => 'route:admin/themes/set-default',
                        ),
                        array(
                            'label' => 'Change Theme settings',
                            'note' => 'Change client themes settings (layout and ...)',
                            'route' => 'route:admin/themes/config',
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }
} 