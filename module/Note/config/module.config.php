<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ajami
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Note;
return array(
    'service_manager' => array(
        'invokables' => array(
            'note_table' => 'Note\Model\NoteTable',
            'note_visibility_table' => 'Note\Model\NoteVisibilityTable',
            'note_api' => 'Note\API\Note',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Note\Controller\Note' => 'Note\Controller\Note',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'note' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/note',
                            'defaults' => array(
                                'controller' => 'Note\Controller\Note',
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
                                        'controller' => 'Note\Controller\Note',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'Note\Controller\Note',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Note\Controller\Note',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                        )
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'contents' => array(
//                'pages' => array(
//                    array(
//                        'label' => 'NOTE',
//                        'route' => 'admin/note',
//                        'resource' => 'route:admin/note',
//                        'pages' => array(
//                            array(
//                                'label' => 'NOTE_NEW',
//                                'route' => 'admin/note/new',
//                                'resource' => 'route:admin/note/new',
//                            ),
//                        )
//                    )
//                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'notes' => 'Note\View\Helper\Notes',
//            'note' => 'Note\View\Helper\Note',
//            'note_loader' => 'Note\View\Helper\NoteLoader',
//            'note_widget' => 'Note\View\Helper\Widget',
        ),
    ),
    'widgets' => array( //        'Note\View\Helper\Widget' => 'NOTE_WIDGET'
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
                'Note' => __DIR__ . '/../public',
            ),
        ),
    ),
);