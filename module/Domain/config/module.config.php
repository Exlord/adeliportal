<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Domain;
return array(
    'service_manager' => array(
        'invokables' => array(
            'domain_api' => 'Domain\API\Domain',
            'domain_table' => 'Domain\Model\DomainTable',
            'domain_content_table' => 'Domain\Model\DomainContentTable',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Domain\Controller\Domain' => 'Domain\Controller\Domain',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'domain' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/domain',
                            'defaults' => array(
                                'controller' => 'Domain\Controller\Domain',
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
                                        'controller' => 'Domain\Controller\Domain',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'Domain\Controller\Domain',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Domain\Controller\Domain',
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
            'structure' => array(
                'pages' => array(
                    array(
                        'label' => 'Domain',
                        'route' => 'admin/domain',
                        'resource' => 'route:admin/domain',
                        'pages' => array(
                            array(
                                'label' => 'New',
                                'route' => 'admin/domain/new',
                            ),
                            array(
                                'label' => 'Edit',
                                'route' => 'admin/domain/edit',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Delete',
                                'route' => 'admin/domain/delete',
                                'visible' => false
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
);