<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:48 AM
 */
namespace ContentSharing\API;

use ContentSharing\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Content Sharing Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_CONTENT_SHARING,
                    'child_route' => array(
                        array(
                            'label' => 'Configs',
                            'note' => '',
                            'route' => Module::ADMIN_CONTENT_SHARING_CONFIG,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );


        return $dataItemAcl;
    }
} 