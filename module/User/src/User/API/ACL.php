<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:19 AM
 */

namespace User\API;


use User\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Users Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_USER,
                    'child_route' => array(
                        array(
                            'label' => "View User Widget's Content",
                            'note' => '',
                            'route' => Module::ADMIN_USER_WIDGET,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update',
                            'note' => 'update user details (status,...)',
                            'route' => Module::ADMIN_USER_UPDATE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Password Reset',
                            'note' => '',
                            'route' => Module::ADMIN_USER_PASSWORD_RESET,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Global Users Configs',
                            'note' => '',
                            'route' => Module::ADMIN_USER_CONFIG,
                            'child_route' => array(
                                array(
                                    'label' => 'Advance Configs',
                                    'note' => '',
                                    'route' => 'route:admin/users/config/more',
                                ),
                            )
                        ),
                        array(
                            'label' => 'Create',
                            'note' => 'Create new user',
                            'route' => Module::ADMIN_USER_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'View User Profile',
                            'note' => '',
                            'route' => Module::ADMIN_USER_VIEW,
                            'child_route' => array(
                                array(
                                    'label' => 'View All',
                                    'note' => 'View All users profiles',
                                    'route' => Module::ADMIN_USER_VIEW_ALL,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'See other users private fields',
                                    'note' => '',
                                    'route' => Module::USER_VIEW_PRIVATE_FIELDS,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Edit User',
                            'note' => 'Edit user profile',
                            'route' => Module::ADMIN_USER_EDIT,
                            'child_route' => array(
                                array(
                                    'label' => 'Edit All',
                                    'note' => 'Edit All users profiles',
                                    'route' => Module::ADMIN_USER_EDIT_ALL,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Change Users Roles',
                                    'note' => '',
                                    'route' => Module::ADMIN_USER_EDIT_ROLE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Edit Image',
                            'note' => 'Edit user profile image',
                            'route' => Module::ADMIN_USER_EDIT_IMAGE,
                            'child_route' => array(
                                array(
                                    'label' => 'Edit All',
                                    'note' => 'Edit All users profile image',
                                    'route' => Module::ADMIN_USER_EDIT_IMAGE_ALL,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Delete User Account',
                            'note' => '',
                            'route' => Module::ADMIN_USER_DELETE,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete All',
                                    'note' => 'Delete All users accounts',
                                    'route' => Module::ADMIN_USER_DELETE_ALL,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete Own Account',
                                    'note' => '',
                                    'route' => Module::ADMIN_USER_DELETE_OWN,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Change Password',
                            'note' => '',
                            'route' => Module::ADMIN_USER_CHANGE_PASSWORD,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'User Roles',
                            'note' => '',
                            'route' => Module::ADMIN_USER_ROLE,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => '',
                                    'route' => Module::ADMIN_USER_ROLE_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => '',
                                    'route' => Module::ADMIN_USER_ROLE_EDIT,
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => '',
                                    'route' => Module::ADMIN_USER_ROLE_DELETE,
                                ),
                            )
                        ),
                        array(
                            'label' => 'User Permissions',
                            'note' => 'a list of permissions(this page)',
                            'route' => Module::ADMIN_USER_PERMISSION,
                            'child_route' => array(
                                array(
                                    'label' => 'Change',
                                    'note' => 'change all user roles permissions(this page) ',
                                    'route' => Module::ADMIN_USER_PERMISSION_CHANGE,
                                    'child_route' => ''
                                ),
                            )
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }

} 