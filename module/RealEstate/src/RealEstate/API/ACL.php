<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:10 AM
 */

namespace RealEstate\API;


use RealEstate\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Real Estate Section',
            'note' => 'Real Estate Section',
            'route' => Module::APP_REAL_ESTATE,
            'child_route' => array(
                array(
                    'label' => 'Application Download',
                    'note' => '',
                    'route' => Module::APP_REAL_ESTATE_APP_DOWNLOAD,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Compare',
                    'note' => '',
                    'route' => Module::APP_REAL_ESTATE_COMPARE,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Export',
                    'note' => '',
                    'route' => Module::APP_REAL_ESTATE_EXPORT,
                    'child_route' => ''
                ),
                array(
                    'label' => 'REALESTATE_SEARCH_BY_MAP',
                    'note' => '',
                    'route' => Module::APP_REAL_ESTATE_SEARCHBYMAP,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Show RealEstate Agent List',
                    'note' => '',
                    'route' => Module::APP_REAL_ESTATE_AGENT,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Upload App Data',
                    'note' => 'Upload data from application software',
                    'route' => Module::APP_REAL_ESTATE_UPLOAD_APP_DATA,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Real Estate List Section',
                    'note' => 'Real Estate List Section',
                    'route' => Module::APP_REAL_ESTATE_LIST,
                    'child_route' => array(
                        array(
                            'label' => 'All Real Estate List',
                            'note' => 'View All Even if by Module not registered',
                            'route' => Module::APP_REAL_ESTATE_LIST_ALL,
                            'child_route' => ''
                        )
                    ),
                ),
                array(
                    'label' => 'Real Estate View Section',
                    'note' => 'Real Estate View Section',
                    'route' => Module::APP_REAL_ESTATE_VIEW,
                    'child_route' => array(
                        array(
                            'label' => 'All Real Estate View',
                            'note' => 'View All Even if by Module not registered',
                            'route' => Module::APP_REAL_ESTATE_VIEW_ALL,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Real Estate View User Info',
                            'note' => '',
                            'route' => Module::APP_REAL_ESTATE_VIEW_ALL_SHOW_USER_INFO,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Real Estate View User Agent Info',
                            'note' => '',
                            'route' => Module::APP_REAL_ESTATE_VIEW_ALL_SHOW_USER_AGENT_INFO,
                            'child_route' => ''
                        )
                    ),
                ),
                array(
                    'label' => 'Estate Transfer',
                    'note' => 'Estate Transfer',
                    'route' => Module::APP_REAL_ESTATE_NEW_TRANSFER,
                    'child_route' => ''
                ),
                /*array(
                    'label' => 'Key Word',
                    'note' => 'Key Word',
                    'route' => Module::APP_REAL_ESTATE_KEYWORD,
                    'child_route' => ''
                ),*/
                array(
                    'label' => 'Guest Edit Permission',
                    'note' => 'Guest Edit Permission',
                    'route' => Module::APP_REAL_ESTATE_EDIT_USER,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Edit',
                    'note' => 'Edit',
                    'route' => Module::APP_REAL_ESTATE_EDIT,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Estate Request',
                    'note' => 'Estate Request',
                    'route' => Module::APP_REAL_ESTATE_NEW_REQUEST,
                    'child_route' => ''
                ),
            ),
        );

        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Real Estate Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_REAL_ESTATE,
                    'child_route' => array(
                        array(
                            'label' => 'All RealEstate',
                            'note' => '',
                            'route' => Module::ADMIN_REAL_ESTATE_ALL,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Agent Area',
                            'note' => '',
                            'route' => Module::ADMIN_REAL_ESTATE_AGENT_AREA,
                            'child_route' => array(
                                array(
                                    'label' => 'Get agent area',
                                    'note' => '',
                                    'route' => Module::ADMIN_REAL_ESTATE_AGENT_AREA_GET,
                                    'child_route' => ''
                                ),
                            )
                        ),
                        array(
                            'label' => 'View Widget Info',
                            'note' => '',
                            'route' => Module::ADMIN_REAL_ESTATE_WIDGET,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Real Estate List Section',
                            'note' => 'Real Estate List Section',
                            'route' => Module::ADMIN_REAL_ESTATE_LIST,
                            'child_route' => array(
                                array(
                                    'label' => 'All Real Estate List',
                                    'note' => 'View All Even if by Module not registered',
                                    'route' => Module::ADMIN_REAL_ESTATE_LIST_ALL,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Configs Section',
                            'note' => 'Configs Section',
                            'route' => Module::ADMIN_REAL_ESTATE_CONFIG,
                            'child_route' => array(
                                array(
                                    'label' => 'Advance Settings',
                                    'note' => 'Advance Settings',
                                    'route' => Module::ADMIN_REAL_ESTATE_CONFIG_MORE,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Estate Transfer',
                            'note' => 'Estate Transfer',
                            'route' => Module::ADMIN_REAL_ESTATE_NEW_TRANSFER,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Estate Request',
                            'note' => 'Estate Request',
                            'route' => Module::ADMIN_REAL_ESTATE_NEW_REQUEST,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit Section',
                            'note' => 'Edit Section',
                            'route' => Module::ADMIN_REAL_ESTATE_EDIT,
                            'child_route' => array(
                                array(
                                    'label' => 'Edit All',
                                    'note' => 'Edit All Even if by Module not registered',
                                    'route' => Module::ADMIN_REAL_ESTATE_EDIT_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Delete Section',
                            'note' => 'Delete Section',
                            'route' => Module::ADMIN_REAL_ESTATE_DELETE,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete All',
                                    'note' => 'Delete All Even if by Module not registered',
                                    'route' => Module::ADMIN_REAL_ESTATE_DELETE_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'View Section',
                            'note' => 'View Section',
                            'route' => Module::ADMIN_REAL_ESTATE_VIEW,
                            'child_route' => array(
                                array(
                                    'label' => 'View All',
                                    'note' => 'View All Even if by Module not registered',
                                    'route' => Module::ADMIN_REAL_ESTATE_VIEW_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Update Section',
                            'note' => 'Update Section',
                            'route' => Module::ADMIN_REAL_ESTATE_UPDATE,
                            'child_route' => array(
                                array(
                                    'label' => 'Update All',
                                    'note' => 'Update All Even if by Module not registered',
                                    'route' => Module::ADMIN_REAL_ESTATE_UPDATE_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Archive',
                            'note' => 'Archive',
                            'route' => Module::ADMIN_REAL_ESTATE_ARCHIVE,
                            'child_route' => array(
                                array(
                                    'label' => 'Archive',
                                    'note' => 'Archive',
                                    'route' => Module::ADMIN_REAL_ESTATE_ARCHIVE_ALL,
                                    'child_route' => ''
                                )
                            )
                        ),
                        array(
                            'label' => 'Word Export',
                            'note' => 'Word Export',
                            'route' => Module::ADMIN_REAL_ESTATE_WORD_EXPORT,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );


        return $dataItemAcl;
    }
} 