<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:15 AM
 */

namespace SiteMap\API;


class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Sitemap Config',
                    'route' => 'route:admin/sitemap',
                ),
            ),
        );

        return $dataItemAcl;
    }
} 