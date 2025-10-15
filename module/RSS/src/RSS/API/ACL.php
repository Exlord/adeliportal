<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:10 AM
 */

namespace RSS\API;


use RSS\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Rss Reader Section',
                    'note' => 'Rss Reader Section',
                    'route' => Module::ADMIN_RSS_READER,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_RSS_READER_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'Edit',
                            'route' => Module::ADMIN_RSS_READER_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_RSS_READER_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update',
                            'note' => 'Update',
                            'route' => Module::ADMIN_RSS_READER_UPDATE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Configs',
                            'note' => 'Configs',
                            'route' => Module::ADMIN_RSS_READER_CONFIG,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }
} 