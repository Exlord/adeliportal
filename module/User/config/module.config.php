<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/26/12
 * Time: 3:00 PM
 */
namespace User;
return array(
    'service_manager' => array(
        'factories' => array(
            'user_auth_adapter' => 'User\Factory\UserAuthAdapter',
            'user_auth_service' => 'User\Factory\UserAuthService',
            'user_identity' => 'User\Factory\UserIdentity',
        ),
        'invokables' => array(
            'user_role_table' => 'User\Model\UserRoleTable',
            'role_table' => 'User\Model\RoleTable',
            'user_profile_table' => 'User\Model\UserProfileTable',
            'user_table' => 'User\Model\UserTable',
            'user_event_manager' => 'User\API\EventManager',
            'user_flood_table' => 'User\Model\Flood',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'users' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/users',
                            'defaults' => array(
                                'controller' => 'User\Controller\User',
                                'action' => 'list',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'auth-access-denied' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/auth-access-denied',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'access-denied'
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'password-reset' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/password-reset[/:id]',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'password-reset',
                                    ),
                                ),
                            ),
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'config',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'more' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/more',
                                            'defaults' => array(
                                                'controller' => 'User\Controller\User',
                                                'action' => 'advance-config',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'new' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'view' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                            'change-password' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/change-password',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'change-password',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'edit',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'custom-profile' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/custom-profile',
                                            'defaults' => array(
                                                'controller' => 'User\Controller\User',
                                                'action' => 'edit-custom-profile',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'edit-image' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit-image',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'edit-image',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'search' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/search',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'search',
                                    ),
                                ),
                            ),
                            'role' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/role',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\Role',
                                        'action' => 'index',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'User\Controller\Role',
                                                'action' => 'new',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'constraints' => array(
                                                'id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'User\Controller\Role',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'constraints' => array(),
                                            'defaults' => array(
                                                'controller' => 'User\Controller\Role',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    )
                                )
                            ),
                            'permission' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/permission',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\Permission',
                                        'action' => 'index',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'permission-rebuild' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/rebuild',
                                            'defaults' => array(
                                                'controller' => 'User\Controller\Permission',
                                                'action' => 'rebuild',
                                            ),
                                        ),
                                    ),
                                    'permission-delete-list' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete-list',
                                            'defaults' => array(
                                                'controller' => 'User\Controller\Permission',
                                                'action' => 'delete-list',
                                            ),
                                        ),
                                    ),
                                    'permission-change' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/change',
                                            'defaults' => array(
                                                'controller' => 'User\Controller\Permission',
                                                'action' => 'change',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'app' => array(
                'child_routes' => array(
                    'user' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/user',
                            'defaults' => array(
                                'controller' => 'User\Controller\User',
                                'action' => 'view',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'user-profile' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                            'auth-access-denied' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/auth-access-denied',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'access-denied'
                                    ),
                                ),
                            ),
                            'login' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/login',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'login'
                                    ),
                                ),
                            ),
                            'verify-email' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/verify-email',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'verify-email'
                                    ),
                                ),
                            ),
                            'password-recovery' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/password-recovery',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'password-recovery'
                                    ),
                                ),
                            ),
                            'logout' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/logout',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'logout'
                                    ),
                                ),
                            ),
                            'register' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/register[/:roleId[/:roleName]]',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\User',
                                        'action' => 'register'
                                    ),
                                ),
                            ),
                            /* 'password-recovery' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/password-recovery[-step:step]',
                                     'constraints' => array(
                                         'step' => '[0-9]+',
                                     ),
                                     'defaults' => array(
                                         'controller' => 'User\Controller\User',
                                         'action' => 'recover-password',
                                         'step' => 1
                                     ),
                                 ),
                             ),*/

                        ),
                    ),
                )
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'User\Controller\Role' => 'User\Controller\RoleController',
            'User\Controller\User' => 'User\Controller\UserController',
            'User\Controller\Permission' => 'User\Controller\PermissionController',
        ),
    ),
//    'controller_plugins' => array(
//        'invokables' => array(
//            'user_auth' => 'User\Controller\Plugin\Auth',
//        )
//    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/' . __NAMESPACE__ . '.lang',
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'error/403' => __DIR__ . '../../view/user/user/access-denied.phtml',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'user_login_block' => 'User\View\Helper\LoginBlock',
            'user_logged_in_block' => 'User\View\Helper\LoggedInBlock',
            'userInfo' => 'User\View\Helper\UserInfo',
            'user_widget' => 'User\View\Helper\Widget',
            'online_users' => 'User\View\Helper\OnlineUsers',
        ),
    ),
    'widgets' => array(
        'User\View\Helper\Widget' => 'Users Widget',
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'user' => __DIR__ . '/../public',
            ),
        ),
    ),
    'navigation' => array(
        'admin_menu' => array(
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Users',
                        'note' => 'user module configuration including : login, register, user-roles and permissions',
                        'route' => 'admin/users/config',
                        'resource' => 'route:admin/users/config',
                        'pages' => array(
                            array(
                                'label' => 'Advance',
                                'route' => 'admin/users/config/more',
                                'resource' => 'route:admin/users/config/more',
                                'pages' => array()
                            )
                        )
                    )
                ),
            ),
            array(
                'label' => 'Members',
                'route' => 'admin/users',
                'resource' => 'route:admin/users',
                'order' => -9000,
                'pages' => array(
                    array(
                        'label' => 'Members',
                        'route' => 'admin/users',
                        'resource' => 'route:admin/users',
                        'pages' => array()
                    ),
                    array(
                        'label' => 'New User',
                        'route' => 'admin/users/new',
                        'resource' => 'route:admin/users/new',
                    ),
                    array(
                        'label' => 'User Profile',
                        'route' => 'admin/users/view',
                        'visible' => false,
                        'pages' => array(
                            array(
                                'label' => 'Edit',
                                'route' => 'admin/users/edit',
                                'visible' => false,
                                'pages' => array(
                                    array(
                                        'label' => 'Edit Custom Profile',
                                        'route' => 'admin/users/edit/custom-profile',
                                        'visible' => false,
                                    ),
                                )
                            ),
                            array(
                                'label' => 'Change password',
                                'route' => 'admin/users/change-password',
                                'visible' => false
                            ),
                        )
                    ),
                    array(
                        'label' => 'Delete User',
                        'route' => 'admin/users/delete',
                        'visible' => false
                    ),
                    array(
                        'label' => 'Roles',
                        'route' => 'admin/users/role',
                        'resource' => 'route:admin/users/role',
                        'pages' => array(
                            array(
                                'label' => 'New Role',
                                'route' => 'admin/users/role/new',
                                'resource' => 'route:admin/users/role/new',
                            ),
                            array(
                                'label' => 'Edit Role',
                                'route' => 'admin/users/role/edit',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Delete Role',
                                'route' => 'admin/users/role/delete',
                                'visible' => false
                            ),
                        )
                    ),
                    array(
                        'label' => 'Permissions',
                        'route' => 'admin/users/permission',
                        'resource' => 'route:admin/users/permission',
                        'pages' => array(
                            array(
                                'route' => 'admin/users/permission/rebuild',
                                'visible' => false
                            ),

                        )
                    ),
                    array(
                        'label' => 'Settings',
                        // 'title' => 'user module configuration including : login, register, user-roles and permissions',
                        'route' => 'admin/users/config',
                        'resource' => 'route:admin/users/config',
                        'pages' => array(
                            array(
                                'label' => 'Advance',
                                'route' => 'admin/users/config/more',
                                'resource' => 'route:admin/users/config/more',
                            )
                        )
                    )
                )
            ),
        )
    ),
    'components' => array(
        'user_login_block' => array(
            'label' => 'User Login Block',
            'description' => 'Provides a form and tools for a user to log into the system',
            'helper' => 'user_login_block',
        ),
        'online_users' => array(
            'label' => 'Online Users',
            'description' => 'Displays the count of online users',
            'helper' => 'online_users',
        ),
    ),

    'template_placeholders' => array(
        'Register User ( Mail )' => array(
            '__USERNAME__' => 'User Name',
            '__PASS__' => 'Password',
            '__DISPLAY_NAME__' => 'Display Name',
            '__URL__' => 'Login page link'
        ),
        'Email Verify ( Mail )' => array(
            '__URL__' => 'email verification link',
            '__DISPLAY_NAME__' => 'Display Name',
        ),
        'Recovery And Reset User Password ( Mail )' => array(
            '__PASS__' => 'Password',
            '__URL__' => 'Login page link',
            '__SITE__' => 'site url'
        ),
        'Recovery User Password ( Sms )' => array(
            '__PASS__' => 'Password',
        ),
        'User Account Status Changed' => array(
            '__USERNAME__' => 'User Name',
            '__USERCODE__' => 'User ID number',
            '__STATUS__' => 'users new status (approved,not approved,banned ...)'
        )
    ),

    'fields_entities' => array(
        'user_profile' => 'User Profile'
    )
);