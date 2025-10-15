<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace FormsManager;
return array(
    'service_manager' => array(
        'invokables' => array(
            'forms_table' => 'FormsManager\Model\FormTable',
            'form_data_table' => 'FormsManager\Model\FormDataTable',
            'form_projects_table' => 'FormsManager\Model\FormProjectsTable',
            'forms_api' => 'FormsManager\API\Form'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'FormsManager\Controller\Forms' => 'FormsManager\Controller\FormsController',
            'FormsManager\Controller\FormsData' => 'FormsManager\Controller\FormsDataController',
            'FormsManager\Controller\FormProjects' => 'FormsManager\Controller\FormProjects',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'new-forms-data' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/new-forms-data/:form-id/:form-title',
                            'defaults' => array(
                                'controller' => 'FormsManager\Controller\FormsData',
                                'action' => 'new',
                                'type' => 'app'
                            ),
                        ),
                    ),
                    'view-form-data' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/form-data/:form-id/:id/:form-title[/:viewMode]',
                            'defaults' => array(
                                'controller' => 'FormsManager\Controller\FormsData',
                                'action' => 'view',
                                'type' => 'app',
                                'viewMode' => 'view'
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'configs' => array(
                        'child_routes' => array(
                            'forms' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/forms',
                                    'defaults' => array(
                                        'controller' => 'FormsManager\Controller\Forms',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                        )
                    ),
                    'forms-data' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/forms-data/:form-id',
                            'defaults' => array(
                                'controller' => 'FormsManager\Controller\FormsData',
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
                                        'controller' => 'FormsManager\Controller\FormsData',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'FormsManager\Controller\FormsData',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'FormsManager\Controller\FormsData',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'view' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/view[/:viewMode]',
                                    'defaults' => array(
                                        'controller' => 'FormsManager\Controller\FormsData',
                                        'action' => 'view',
                                        'viewMode' => 'view'
                                    ),
                                ),
                            ),
                        )
                    ),
                    'forms' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/forms',
                            'defaults' => array(
                                'controller' => 'FormsManager\Controller\Forms',
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
                                        'controller' => 'FormsManager\Controller\Forms',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'FormsManager\Controller\Forms',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'FormsManager\Controller\Forms',
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
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Forms',
                        'route' => 'admin/configs/forms',
                        'resource' => 'route:admin/configs/forms',
                    )
                ),
            ),
            'structure' => array(
                'pages' => array(
                    array(
                        'label' => 'Forms Manager',
                        'route' => 'admin/forms',
                        'resource' => 'route:admin/forms',
                        'pages' => array(
                            array(
                                'label' => 'New Form',
                                'route' => 'admin/forms/new',
                                'resource' => 'route:admin/forms',
                            ),
                            array(
                                'label' => 'Edit Form',
                                'route' => 'admin/forms/edit',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Form Data',
                                'route' => 'admin/forms-data',
                                'visible' => false,
                                'pages' => array(
                                    array(
                                        'label' => 'Edit Form Data',
                                        'route' => 'admin/forms-data/edit',
                                        'visible' => false,
                                    ),
                                    array(
                                        'label' => 'View Form Data',
                                        'route' => 'admin/forms-data/view',
                                        'visible' => false,
                                    ),
                                )
                            ),
                        )
                    )
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(),
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
                'forms' => __DIR__ . '/../public',
            ),
        ),
    ),
    'fields_entities' => array(
        'form_manager' => 'Form Manager'
    )
);