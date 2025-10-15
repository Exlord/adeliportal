<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:04 AM
 */

namespace Page\API;


use Page\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'View Static Pages',
            'note' => 'View Static Pages',
            'route' => Module::APP_PAGE_VIEW,
        );
        $dataItemAcl[] = array(
            'label' => 'View Contents',
            'note' => 'View Contents',
            'route' => Module::APP_CONTENT,
        );
        $dataItemAcl[] = array(
            'label' => 'View Single Contents',
            'note' => 'View Single Contents',
            'route' => Module::APP_SINGLE_CONTENT,
        );

        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Configs',
                    'note' => '',
                    'route' => Module::ADMIN_PAGE_CONFIG,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Static Page List',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_PAGE_LIST,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Widget',
                    'note' => '',
                    'route' => Module::ADMIN_PAGE_WIDGET,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Static Pages Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_PAGE,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_PAGE_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit Section',
                            'note' => 'Edit Section',
                            'route' => Module::ADMIN_PAGE_EDIT,
                            'child_route' => array(
                                array(
                                    'label' => 'Edit All',
                                    'note' => 'Edit All Even if by Module not registered',
                                    'route' => Module::ADMIN_PAGE_EDIT_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Delete Section',
                            'note' => 'Delete Section',
                            'route' => Module::ADMIN_PAGE_DELETE,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete All',
                                    'note' => 'Delete All Even if by Module not registered',
                                    'route' => Module::ADMIN_PAGE_DELETE_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Update Section',
                            'note' => 'Update Section',
                            'route' => Module::ADMIN_PAGE_UPDATE,
                            'child_route' => array(
                                array(
                                    'label' => 'Update All',
                                    'note' => 'Update All Even if by Module not registered',
                                    'route' => Module::ADMIN_PAGE_UPDATE_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                    ),
                ),
                array(
                    'label' => 'Contents Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_CONTENT,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_CONTENT_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit Section',
                            'note' => 'Edit Section',
                            'route' => Module::ADMIN_CONTENT_EDIT,
                            'child_route' => array(
                                array(
                                    'label' => 'Edit All',
                                    'note' => 'Edit All Even if by Module not registered',
                                    'route' => Module::ADMIN_CONTENT_EDIT_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Delete Section',
                            'note' => 'Delete Section',
                            'route' => Module::ADMIN_CONTENT_DELETE,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete All',
                                    'note' => 'Delete All Even if by Module not registered',
                                    'route' => Module::ADMIN_CONTENT_DELETE_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Update Section',
                            'note' => 'Update Section',
                            'route' => Module::ADMIN_CONTENT_UPDATE,
                            'child_route' => array(
                                array(
                                    'label' => 'Update All',
                                    'note' => 'Update All Even if by Module not registered',
                                    'route' => Module::ADMIN_CONTENT_UPDATE_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                    ),
                ),
                /* array(
                     'label' => 'Auto complete',
                     'note' => 'Access admin area auto complete',
                     'route' => Module::ADMIN_AUTO_COMPLETE,
                     'child_route' => ''
                 ),*/
            ),
        );

        return $dataItemAcl;
    }
} 