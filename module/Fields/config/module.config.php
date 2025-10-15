<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Fields;
return array(
    'service_manager' => array(
        'invokables' => array(
            'Fields\Form\Element\UniqueCode' => 'Fields\Form\Element\UniqueCode',
            'fields_table' => 'Fields\Model\FieldTable',
            'fields_api' => 'Fields\API\Fields',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Fields\Controller\Field' => 'Fields\Controller\FieldController',
            'Fields\Controller\FieldGroup' => 'Fields\Controller\FieldGroup',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'fieldBarcode' => 'Fields\Form\View\Helper\FieldBarcode',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'fields' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/fields[/:entityType]',
                            'defaults' => array(
                                'controller' => 'Fields\Controller\Field',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'Fields\Controller\Field',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'Fields\Controller\Field',
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
                                        'controller' => 'Fields\Controller\Field',
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
                                        'controller' => 'Fields\Controller\Field',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'groups' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/groups',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'Fields\Controller\FieldGroup',
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
                                                'controller' => 'Fields\Controller\FieldGroup',
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
                                                'controller' => 'Fields\Controller\FieldGroup',
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
                                                'controller' => 'Fields\Controller\FieldGroup',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'structure' => array(
                'pages' => array(
                    array(
                        'label' => 'Fields',
                        'route' => 'admin/fields',
                        'resource' => 'route:admin/fields',
                        'pages' => array(
                            array(
                                'label' => 'New Field',
                                'route' => 'admin/fields/new',
                                'resource' => 'route:admin/fields/new',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Edit Field',
                                'route' => 'admin/fields/edit',
                                'resource' => 'route:admin/fields/edit',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/fields/delete',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Field Groups',
                                'route' => 'admin/fields/groups',
                                'resource' => 'route:admin/fields/groups',
                                'visible' => false,
                                'pages' => array(
                                    array(
                                        'label' => 'Field Groups',
                                        'route' => 'admin/fields/groups',
                                        'resource' => 'route:admin/fields/groups',
                                        'visible' => false,
                                        'pages' => array()
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
                'fields' => __DIR__ . '/../public',
            ),
        ),
    ),
);