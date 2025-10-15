<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:43 AM
 */

namespace Analyzer\API;


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
                            'label' => 'Analyzer Config',
                            'note' => '',
                            'route' => 'route:admin/configs/analyzer',
                        )
                    )
                ),
                array(
                    'route' => 'route:admin/reports',
                    'child_route' => array(
                        array(
                            'label' => 'Analyzer Report',
                            'note' => 'A graph of user visits and ...',
                            'route' => 'route:admin/reports/analyzer',
                        )
                    )
                )
            )
        );

        return $dataItemAcl;
    }
} 