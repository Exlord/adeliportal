<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:09 AM
 */

namespace Rating\API;


use Rating\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Rating Section',
            'note' => 'Rating Section',
            'route' => Module::APP_RATING,
            'child_route' => array(
                array(
                    'label' => 'Create',
                    'note' => 'Create',
                    'route' => Module::APP_RATING_NEW,
                    'child_route' => ''
                )
            )
        );
        $dataItemAcl[] = array(
            'label' => 'Negative Positive Rate Section',
            'note' => 'Negative Positive Rate Section',
            'route' => Module::APP_NP,
            'child_route' => array(
                array(
                    'label' => 'Create',
                    'note' => 'Create',
                    'route' => Module::APP_NP_NEW,
                    'child_route' => ''
                )
            )
        );


        return $dataItemAcl;
    }
} 