<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:54 AM
 */

namespace File\API;


use File\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Delete Files',
            'note' => 'Allow deleting a file uploaded for a entity',
            'access' => 1,
            'route' => Module::APP_DELETE_FILE,
        );
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Files Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_FILE,
                    'child_route' => array(
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_FILE_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'File Upload',
                            'note' => 'File Upload',
                            'route' => Module::ADMIN_FILE_CONNECTOR,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Public Files Section',
                            'note' => 'Public Files Section',
                            'route' => Module::ADMIN_FILE_PUBLIC,
                            'child_route' => array(
                                array(
                                    'label' => 'View All',
                                    'note' => 'View All Even if by Module not registered',
                                    'route' => Module::ADMIN_FILE_PUBLIC_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Private Files Section',
                            'note' => 'Private Files Section',
                            'route' => Module::ADMIN_FILE_PRIVATE,
                            'child_route' => array(
                                array(
                                    'label' => 'New',
                                    'note' => '',
                                    'route' => 'route:admin/file/private/new',
                                    'child_route' => array()
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => '',
                                    'route' => 'route:admin/file/private/edit',
                                    'child_route' => array()
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => '',
                                    'route' => 'route:admin/file/private/delete',
                                    'child_route' => array()
                                ),
                            )
                        ),
                    ),
                ),
            ),
        );

        return $dataItemAcl;
    }
} 