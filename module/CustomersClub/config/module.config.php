<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace CustomersClub;
return array(
    'service_manager' => array(
        'invokables' => array(
            'points_table' => 'CustomersClub\Model\PointsTable',
            'points_total_table' => 'CustomersClub\Model\PointsTotalTable',
            'customer_records_table' => 'CustomersClub\Model\CustomerRecordsTable',
            'points_api' => 'CustomersClub\API\Point',
            'cc_api' => 'CustomersClub\API\Club',
            'cc_event_manager' => 'CustomersClub\API\EventManager',
        ),

    ),
    'controllers' => array(
        'invokables' => array(
            'CustomersClub\Controller\Club' => 'CustomersClub\Controller\Club',
            'CustomersClub\Controller\Point' => 'CustomersClub\Controller\Point',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'customers-club' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/customers-club',
                            'defaults' => array(
                                'controller' => 'CustomersClub\Controller\Club',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'CustomersClub\Controller\Club',
                                        'action' => 'config',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'my-points' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/my-points',
                                    'defaults' => array(
                                        'controller' => 'CustomersClub\Controller\Point',
                                        'action' => 'my-points',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'points' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/points',
                                    'defaults' => array(
                                        'controller' => 'CustomersClub\Controller\Point',
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
                                                'controller' => 'CustomersClub\Controller\Point',
                                                'action' => 'new',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array()
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'CustomersClub\Controller\Point',
                                                'action' => 'edit',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array()
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'CustomersClub\Controller\Point',
                                                'action' => 'delete',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array()
                                    ),
                                )
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
                        'label' => 'Customers Club',
                        'route' => 'admin/customers-club/config',
                        'resource' => 'route:admin/customers-club/config',
                    ),
                )
            ),
            'modules' => array(
                'pages' => array(
                    array(
                        'label' => 'Customers Club',
                        'route' => 'admin/customers-club',
                        'resource' => 'route:admin/customers-club',
                        'pages' => array(
                            array(
                                'label' => 'Config',
                                'route' => 'admin/customers-club/config',
                                'resource' => 'route:admin/customers-club/config',
                            ),
                            array(
                                'label' => 'My Points',
                                'route' => 'admin/customers-club/my-points',
                                'resource' => 'route:admin/customers-club/my-points',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Points',
                                'route' => 'admin/customers-club/points',
                                'resource' => 'route:admin/customers-club/points',
                                'pages' => array(
                                    array(
                                        'label' => 'Add Point',
                                        'route' => 'admin/customers-club/points/new',
                                        'resource' => 'route:admin/customers-club/points/new',
                                        'pages' => array()
                                    ),
                                    array(
                                        'label' => 'Edit',
                                        'route' => 'admin/customers-club/points/edit',
                                        'resource' => 'route:admin/customers-club/points/edit',
                                        'visible' => false
                                    )
                                )
                            )
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
        'invokables' => array(
            'customer_records' => 'CustomersClub\View\Helper\CustomerRecords',
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
                'CustomersClub' => __DIR__ . '/../public',
            ),
        ),
    ),
);