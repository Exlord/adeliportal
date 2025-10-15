<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/14/13
 * Time: 10:36 AM
 */

namespace Gallery\API;


class Gallery {
    public static function getNavigation($label, $type)
    {
        $navigation = array(
            'label' => $label,
            'route' => 'admin/' . $type,
            'resource' => 'route:admin/' . $type,
            'pages' => array(
                array(
                    'label' => 'Groups',
                    'route' => 'admin/' . $type . '/groups',
                    'resource' => 'route:admin/' . $type . '/groups',
                    'pages' => array(
                        array(
                            'label' => 'New Group',
                            'route' => 'admin/' . $type . '/groups/new',
                            'resource' => 'route:admin/' . $type . '/groups/new',
                        ),
                    )
                ),
                array(
                    'label' => 'Items',
                    'route' => 'admin/' . $type . '/item',
                    'resource' => 'route:admin/' . $type . '/item',
                    'pages' => array(
                        array(
                            'label' => 'New Item',
                            'route' => 'admin/' . $type . '/item/new',
                            'resource' => 'route:admin/' . $type . '/item/new',
                        ),
                    ),
                ),
            ),
        );
        return $navigation;
    }

} 