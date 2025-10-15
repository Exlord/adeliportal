<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:45 AM
 */

namespace Comment\API;


use Comment\Module;

class ACL {
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Comments Section',
            'note' => 'Comments Section',
            'route' => Module::APP_COMMENT,
            'child_route' => array(
                array(
                    'label' => 'Create',
                    'note' => 'Create',
                    'route' => Module::APP_COMMENT_NEW,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Edit Section',
                    'note' => 'Edit Section',
                    'route' => Module::APP_COMMENT_EDIT,
                    'child_route' => array(
                        array(
                            'label' => 'Edit All',
                            'note' => 'Edit All Even if by Module not registered',
                            'route' => Module::APP_COMMENT_EDIT_ALL,
                            'child_route' => ''
                        ),
                    ),
                ),
                array(
                    'label' => 'Delete Section',
                    'note' => 'Delete Section',
                    'route' => Module::APP_COMMENT_DELETE,
                    'child_route' => array(
                        array(
                            'label' => 'Delete All',
                            'note' => 'Delete All Even if by Module not registered',
                            'route' => Module::APP_COMMENT_DELETE_ALL,
                            'child_route' => ''
                        ),
                    ),
                ),
            ),
        );

        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Comments Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_COMMENT,
                    'child_route' => array(
                        array(
                            'label' => 'Approve',
                            'note' => 'allow approving the unapproved comments',
                            'route' => Module::ADMIN_COMMENT_APPROVE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'View All',
                            'note' => 'View All Even if by Module not registered',
                            'route' => Module::ADMIN_COMMENT_ALL,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_COMMENT_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit Section',
                            'note' => 'Edit Section',
                            'route' => Module::ADMIN_COMMENT_EDIT,
                            'child_route' => array(
                                array(
                                    'label' => 'Edit All',
                                    'note' => 'Edit All Even if by Module not registered',
                                    'route' => Module::ADMIN_COMMENT_EDIT_ALL,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Delete Section',
                            'note' => 'Delete Section',
                            'route' => Module::ADMIN_COMMENT_DELETE,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete All',
                                    'note' => 'Delete All Even if by Module not registered',
                                    'route' => Module::ADMIN_COMMENT_DELETE_ALL,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Update Section',
                            'note' => 'Update Section',
                            'route' => Module::ADMIN_COMMENT_UPDATE,
                            'child_route' => array(
                                array(
                                    'label' => 'Update All',
                                    'note' => 'Update All Even if by Module not registered',
                                    'route' => Module::ADMIN_COMMENT_UPDATE_ALL,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Configs',
                            'note' => 'Configs',
                            'route' => Module::ADMIN_COMMENT_CONFIG,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );


            return $dataItemAcl;
    }

} 