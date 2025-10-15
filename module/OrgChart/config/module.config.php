<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace OrgChart;
return array(
    'service_manager' => array(
        'invokables' => array(
            'org_chart_table' => 'OrgChart\Model\OrgChartTable',
            'chart_node_table' => 'OrgChart\Model\ChartNodeTable',
            'org_chart_event_manager' => 'OrgChart\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'OrgChart\Controller\OrgChartAdmin' => 'OrgChart\Controller\ChartAdmin',
            'OrgChart\Controller\OrgChartClient' => 'OrgChart\Controller\ChartClient',
            'OrgChart\Controller\ChartNodeAdmin' => 'OrgChart\Controller\NodeAdmin',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'org-chart' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/org-chart',
                            'defaults' => array(
                                'controller' => 'OrgChart\Controller\OrgChartAdmin',
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
                                        'controller' => 'OrgChart\Controller\OrgChartAdmin',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                            'chart-list' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/chart-list',
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\OrgChartAdmin',
                                        'action' => 'chart-list',
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\OrgChartAdmin',
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
                                        'controller' => 'OrgChart\Controller\OrgChartAdmin',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\OrgChartAdmin',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\OrgChartAdmin',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'chart-node' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/chart-node',
                            'defaults' => array(
                                'controller' => 'OrgChart\Controller\ChartNodeAdmin',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'items' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:chartId/items[/:parentId]',
                                    'constraints' => array(
                                        'chartId' => '[0-9]+',
                                        'parentId' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\ChartNodeAdmin',
                                        'action' => 'index',
                                        'parentId' => 0,
                                    ),
                                ),
                            ),
                            'parent-node' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/parent-node',
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\ChartNodeAdmin',
                                        'action' => 'get-parent-node',
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\ChartNodeAdmin',
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
                                        'controller' => 'OrgChart\Controller\ChartNodeAdmin',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\ChartNodeAdmin',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\ChartNodeAdmin',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'app' => array(
                'child_routes' => array(
                    'chart' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/chart/:chartId[/:title]',
                            'defaults' => array(
                                'controller' => 'OrgChart\Controller\OrgChartClient',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(/*'new' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'OrgChart\Controller\OrgChartAdmin',
                                        'action' => 'new',
                                    ),
                                ),
                            ),*/
                        ),
                    ),

                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'modules' => array(
                'pages' => array(
                    array(
                        'label' => 'OrgChart_CHART',
                        'route' => 'admin/org-chart',
                        'resource' => 'route:admin/org-chart',
                        'pages' => array(
                            array(
                                'label' => 'OrgChart_CHARTS',
                                'route' => 'admin/org-chart',
                                'resource' => 'route:admin/org-chart',
                                'pages' => array(
                                    array(
                                        'label' => 'OrgChart_NEWCHART',
                                        'route' => 'admin/org-chart/new',
                                        'resource' => 'route:admin/org-chart/new',
                                    ),
                                ),
                            ),
                            array(
                                'label' => 'OrgChart_NODESCHART',
                                'route' => 'admin/chart-node',
                                'resource' => 'route:admin/chart-node',
                                'pages' => array(
                                    array(
                                        'label' => 'OrgChart_NEWNODECHART',
                                        'route' => 'admin/chart-node/new',
                                        'resource' => 'route:admin/chart-node/new',
                                    ),
                                ),
                            ),
                        )
                    )
                ),
            ),
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'OrgChart_CHART',
                        'route' => 'admin/org-chart/config',
                        'resource' => 'route:admin/org-chart/config',
                    ),
                )
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array( // 'sample_helper' => 'OnlineOrders\View\Helper\OnlineOrders',
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
                'OrgChart' => __DIR__ . '/../public',
            ),
        ),
    ),
);