<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace GeographicalAreas;
return array(
    'service_manager' => array(
        'invokables' => array(
            'country_table' => 'GeographicalAreas\Model\CountryTable',
            'state_table' => 'GeographicalAreas\Model\StateTable',
            'city_table' => 'GeographicalAreas\Model\CityTable',
            'city_area_table' => 'GeographicalAreas\Model\CityAreaTable',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'GeographicalAreas\Controller\Country' => 'GeographicalAreas\Controller\CountryController',
            'GeographicalAreas\Controller\Index' => 'GeographicalAreas\Controller\IndexController',
            'GeographicalAreas\Controller\State' => 'GeographicalAreas\Controller\StateController',
            'GeographicalAreas\Controller\City' => 'GeographicalAreas\Controller\CityController',
            'GeographicalAreas\Controller\Area' => 'GeographicalAreas\Controller\AreaController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'geographical-areas-get-sub-area-list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/geographical-areas-get-sub-area-list/:areaId',
                    'defaults' => array(
                        'controller' => 'GeographicalAreas\Controller\Area',
                        'action' => 'get-sub-list',
                    ),
                ),
            ),
            'geographical-areas-get-area-list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/geographical-areas-get-area-list/:cityId',
                    'defaults' => array(
                        'controller' => 'GeographicalAreas\Controller\Area',
                        'action' => 'get-list',
                    ),
                ),
            ),
            'geographical-areas-get-city-list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/geographical-areas-get-city-list/:stateId',
                    'defaults' => array(
                        'controller' => 'GeographicalAreas\Controller\City',
                        'action' => 'get-list',
                    ),
                ),
            ),
            'geographical-areas-get-state-list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/geographical-areas-get-state-list/:countryId',
                    'defaults' => array(
                        'controller' => 'GeographicalAreas\Controller\State',
                        'action' => 'get-list',
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'geographical-areas' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/geographical-areas',
                            'defaults' => array(
                                'controller' => 'GeographicalAreas\Controller\Index',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'country' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/country',
                                    'defaults' => array(
                                        'controller' => 'GeographicalAreas\Controller\Country',
                                        'action' => 'index',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    /*'query' => array(
                                        'type' => 'Query',
                                    ),*/
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\Country',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\Country',
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
                                                'controller' => 'GeographicalAreas\Controller\Country',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'constraints' => array(
                                            ),
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\Country',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'state' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/state[/:countryId]',
                                    'constraints' => array(
                                        'countryId' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'GeographicalAreas\Controller\State',
                                        'action' => 'index',
                                        'countryId' => 0,
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    /*'query' => array(
                                        'type' => 'Query',
                                    ),*/
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\State',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\State',
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
                                                'controller' => 'GeographicalAreas\Controller\State',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'constraints' => array(
                                            ),
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\State',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'city' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/city[/:stateId]',
                                    'constraints' => array(
                                        'stateId' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'GeographicalAreas\Controller\City',
                                        'action' => 'index',
                                        'stateId' => 0,
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    /* 'query' => array(
                                         'type' => 'Query',
                                     ),*/
                                    'update' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\City',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\City',
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
                                                'controller' => 'GeographicalAreas\Controller\City',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete',
                                            'constraints' => array(
                                            ),
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\City',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'area' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:cityId/area[/:parentId]',
                                    'constraints' => array(
                                        'stateId' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'GeographicalAreas\Controller\Area',
                                        'action' => 'index',
                                        'cityId' => 0,
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    /* 'query' => array(
                                         'type' => 'Query',
                                     ),*/
                                    'update' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\Area',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\Area',
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
                                                'controller' => 'GeographicalAreas\Controller\Area',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete',
                                            'constraints' => array(
                                            ),
                                            'defaults' => array(
                                                'controller' => 'GeographicalAreas\Controller\Area',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'admin_menu' => array(
            'contents' => array(
                'pages' => array(
                    array(
                        'label' => 'Geographical Areas',
                        'route' => 'admin/geographical-areas',
                        'resource' => 'route:admin/geographical-areas',
                        'pages' => array(
                            array(
                                'route' => 'admin/geographical-areas/country',
                                'label' => 'Countries',
                                'pages' => array(
                                    array(
                                        'route' => 'admin/geographical-areas/country/new',
                                        'label' => 'New Country',
                                    ),
                                    array(
                                        'route' => 'admin/geographical-areas/country/edit',
                                        'visible' => false
                                    ),
                                    array(
                                        'route' => 'admin/geographical-areas/country/delete',
                                        'visible' => false
                                    ),
                                )
                            ),
                            array(
                                'route' => 'admin/geographical-areas/state',
                                'label' => 'States',
                                'pages' => array(
                                    array(
                                        'route' => 'admin/geographical-areas/state/new',
                                        'label' => 'New State',
                                    ),
                                    array(
                                        'route' => 'admin/geographical-areas/state/edit',
                                        'visible' => false
                                    ),
                                    array(
                                        'route' => 'admin/geographical-areas/state/delete',
                                        'visible' => false
                                    ),
                                )
                            ),
                            array(
                                'route' => 'admin/geographical-areas/city',
                                'label' => 'Cities',
                                'pages' => array(
                                    array(
                                        'route' => 'admin/geographical-areas/city/new',
                                        'label' => 'New City',
                                    ),
                                    array(
                                        'route' => 'admin/geographical-areas/city/edit',
                                        'visible' => false
                                    ),
                                    array(
                                        'route' => 'admin/geographical-areas/city/delete',
                                        'visible' => false
                                    ),
                                )
                            ),
                            array(
                                'route' => 'admin/geographical-areas/area',
                                'label' => 'Region',
                                'pages' => array(
                                    array(
                                        'route' => 'admin/geographical-areas/area/new',
                                        'label' => 'New Region',
                                        'params'=>array('parentId'=>0),
                                    ),
                                    array(
                                        'route' => 'admin/geographical-areas/area/edit',
                                        'visible' => false
                                    ),
                                    array(
                                        'route' => 'admin/geographical-areas/area/delete',
                                        'visible' => false
                                    ),
                                )
                            )
                        )
                    ),
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
                __DIR__ . '/../public',
            ),
        ),
    ),
    'GeographicalAreas' => array(
        'name' => 'GeographicalAreas',
        'note' => 'Countries ,states and cites',
        'can_be_disabled' => false,
        'client_usable' => false,
        'depends' => array('System'),
        'stage' => 'Active',
        'version'=>'1.0'
    ),
);