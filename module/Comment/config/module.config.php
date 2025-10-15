<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Comment;
return array(
    'service_manager' => array(
        'invokables' => array(
            'comment_table' => 'Comment\Model\CommentTable',
            'comment_event_manager' => 'Comment\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Comment\Controller\Comment' => 'Comment\Controller\CommentController',
            'Comment\Controller\CommentAdmin' => 'Comment\Controller\CommentAdminController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'comment' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/comment',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Comment\Controller\Comment',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'new' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'Comment\Controller\Comment',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/edit',
                                    'defaults' => array(
                                        'controller' => 'Comment\Controller\Comment',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Comment\Controller\Comment',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'comment' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/comment',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Comment\Controller\CommentAdmin',
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
                                        'controller' => 'Comment\Controller\CommentAdmin',
                                        'action' => 'new',

                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'Comment\Controller\CommentAdmin',
                                        'action' => 'edit',

                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Comment\Controller\CommentAdmin',
                                        'action' => 'delete',

                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'Comment\Controller\CommentAdmin',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'Comment\Controller\CommentAdmin',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'contents' => array(
                'pages' => array(
                    array(
                        'label' => 'Comments',
                        'route' => 'admin/comment',
                        'resource' => 'route:admin/comment',
                    ),
                ),
            ),
            'configs'=>array(
                'pages' => array(
                    array(
                        'label' => 'Comments Configs',
                        'route' => 'admin/comment/config',
                        'resource' => 'route:admin/comment/config',
                    ),
                ),
            )
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'comment' => 'Comment\View\Helper\Comment',
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/' . __NAMESPACE__ . '.lang',
            ),
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'Comment' => __DIR__ . '/../public',
            ),
        ),
    ),
);