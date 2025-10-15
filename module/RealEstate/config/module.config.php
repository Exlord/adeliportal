<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace RealEstate;
return array(
    'service_manager' => array(
        'invokables' => array(
            'real_estate_table' => 'RealEstate\Model\RealEstateTable',
            'agent_area_table' => 'RealEstate\Model\AgentAreaTable',
            'real_estate_api' => 'RealEstate\API\RealEstate',
            'real_estate_event_manager' => 'RealEstate\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'RealEstate\Controller\RealEstate' => 'RealEstate\Controller\RealEstateController',
            'RealEstate\Controller\RealEstateAdmin' => 'RealEstate\Controller\RealEstateAdminController',
            'RealEstate\Controller\OfflineApp' => 'RealEstate\Controller\OfflineAppController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'real-estate' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/real-estate',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'RealEstate\Controller\RealEstate',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'export' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/export',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'export',
                                    ),
                                ),
                            ),
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/list',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'list',
                                    ),
                                ),
                            ),
                            'statistic' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/statistic',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'statistic',
                                    ),
                                ),
                            ),
                            'compare' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/compare',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'compare',
                                    ),
                                ),
                            ),
                            'search-by-map' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/search-by-map',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'search-by-map',
                                    ),
                                ),
                            ),
                            'region-statistic' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/region-statistic',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'region-statistic',
                                    ),
                                ),
                            ),
                            'app-download' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/app-download',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'app-download',
                                    ),
                                ),
                            ),
                            'latest-real-estate-reg-type' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/latest-real-estate-reg-type',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'latest-real-estate-reg-type',
                                    ),
                                ),
                            ),
                            'agent' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/agent',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'real-estate-agent',
                                    ),
                                ),
                            ),
                            'view-all-info-estate' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/view-all-info-estate[/:params]',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'view-all-info-estate',
                                    ),
                                ),
                            ),
                            'validate-special-and-info-estate' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/validate-estate[/:params]',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'validate-special-and-info-estate',
                                    ),
                                ),
                            ),
                            'update-offline-app-data' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/update-offline-app-data',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\OfflineApp',
                                        'action' => 'update-data',
                                    ),
                                ),
                            ),
                            'upload-app-data' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/upload-app-data',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\OfflineApp',
                                        'action' => 'upload-app-data',
                                    ),
                                ),
                            ),
                            'view' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/[:id]-[:title].html',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                            'new-transfer' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new-transfer',
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                        'action' => 'new',
                                        'route-type' => 'transfer'
                                    ),
                                ),
                            ),

                            /* 'keyword' => array(
                                 'type' => 'segment',
                                 'options' => array(
                                     'route' => '/:status/:keyword/',
                                     'defaults' => array(
                                         'controller' => 'RealEstate\Controller\RealEstate',
                                         'action' => 'index'
                                     ),
                                 ),
                             ),*/
                            'edit-user' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/edit-user',
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'editUser'
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
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'new-request' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new-request',
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                        'action' => 'new',
                                        'route-type' => 'request'
                                    ),
                                ),
                            ),
                        )
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'real-estate' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/real-estate',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'statistics' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => 'statistics',
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                        'action' => 'statistics',
                                    ),
                                ),
                            ),
                            'agent-area' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/agent-area',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                        'action' => 'agent-area',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'get-agent-area' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/get-agent-area',
                                            'defaults' => array(
                                                'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                                'action' => 'get-agent-area',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/list',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'list',
                                    ),
                                ),
                            ),

                            /* 'export' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/export/:isExport/:exportType/:exportId',
                                     'defaults' => array(
                                         'controller' => 'RealEstate\Controller\RealEstate',
                                         'action' => 'index'
                                     ),
                                 ),
                             ),*/
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
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
                                                'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                                'action' => 'config-more',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'new-transfer' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new-transfer',
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                        'action' => 'new',
                                        'route-type' => 'transfer'
                                    ),
                                ),
                            ),
                            'new-request' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/new-request',
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                        'action' => 'new',
                                        'route-type' => 'request'
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
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'constraints' => array( //  'id' => '[0-9]+',
                                        //  'type' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                        'action' => 'delete',
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
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                            'archive' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/archive',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'archive',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstateAdmin',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'exp-word' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/exp-word',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'RealEstate\Controller\RealEstate',
                                        'action' => 'exp-word',
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
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Real Estate',
                        'route' => 'admin/real-estate/config',
                        'resource' => 'route:admin/real-estate/config',
                        'pages' => array(
                            array(
                                'label' => 'Advance Config',
                                'route' => 'admin/real-estate/config/more',
//                                'visible' => false
                            )
                        )
                    ),
                )
            ),
            'real_estate' => array(
                'label' => 'Real Estate',
                'route' => 'admin/real-estate',
                'resource' => 'route:admin/real-estate',
                'order' => -8000,
                'pages' => array(
                    array(
                        'label' => 'All Estates',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                    ),
                    array(
                        'label' => 'Active Estates',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                        'query' => 'grid_filter_status=1'
                    ),
                    array(
                        'label' => 'Not-Verified Estates',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                        'query' => 'grid_filter_status=0'
                    ),
                    array(
                        'label' => 'Requested Estates',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                        'query' => 'grid_filter_isRequest=1'
                    ),
                    array(
                        'label' => 'Expired Estates',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                        'query' => 'grid_filter_expire=0'
                    ),
                    array(
                        'label' => 'Special Estate',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                        'query' => 'grid_filter_isSpecial=1'
                    ),
                    array(
                        'label' => 'Transferred Estates',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                        'query' => 'grid_filter_status=3'
                    ),
                    array(
                        'label' => 'Canceled Estates',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                        'query' => 'grid_filter_status=4'
                    ),
                    array(
                        'label' => 'Archived Estates',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                        'query' => 'grid_filter_status=2'
                    ),
                    array(
                        'label' => 'Recycle Estates',
                        'route' => 'admin/real-estate',
                        'resource' => 'route:admin/real-estate',
                        'query' => 'grid_filter_status=5'
                    ),
                    array(
                        'label' => 'New Estate Transfer',
                        'route' => 'admin/real-estate/new-transfer',
                        'resource' => 'route:admin/real-estate/new-transfer',
                    ),
                    array(
                        'label' => 'New Estate Request',
                        'route' => 'admin/real-estate/new-request',
                        'resource' => 'route:admin/real-estate/new-request',
                    ),
                    array(
                        'label' => 'Real Estate Agent List',
                        'route' => 'admin/users',
                        'resource' => 'route:admin/users',
                        'query' => 'grid_filter_roleId=11'
                    ),
                    array(
                        'label' => 'Area Admin',
                        'route' => 'admin/real-estate/agent-area',
                        'resource' => 'route:admin/real-estate/agent-area',
                    ),
                    array(
                        'label' => 'Config',
                        'route' => 'admin/real-estate/config',
                        'resource' => 'route:admin/real-estate/config',
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
    'components' => array(
        'latest_real_estate_block' => array(
            'label' => 'Latest RealEstate',
            'description' => '',
            'helper' => 'latest_real_estate_block',
        ),
        'real_estate_block' => array(
            'label' => 'RealEstate',
            'description' => '',
            'helper' => 'real_estate_block',
        ),
        'statistics_real_estate_block' => array(
            'label' => 'REALESTATE_STATISTIC',
            'description' => '',
            'helper' => 'statistics_real_estate_block',
        ),
        'real_estate_search_block' => array(
            'label' => 'REALESTATE_SEARCH',
            'description' => '',
            'helper' => 'real_estate_search_block',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'estate_type_block' => 'RealEstate\View\Helper\EstateTypeBlock',
            'realestate_widget' => 'RealEstate\View\Helper\Widget',
            'realestate_list' => 'RealEstate\View\Helper\EstateList',
            'latest_real_estate_block' => 'RealEstate\View\Helper\LatestRealEstateBlock',
            'real_estate_block' => 'RealEstate\View\Helper\RealEstateBlock',
            'statistics_real_estate_block' => 'RealEstate\View\Helper\StatisticsRealEstateBlock',
            'real_estate_search_block' => 'RealEstate\View\Helper\Search',
        ),
    ),
    'news_letter' => array(
        '\RealEstate\API\RealEstate' => 'Real Estate',
    ),
    'widgets' => array(
        'RealEstate\View\Helper\Widget' => 'Real Estate Widget'
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'real-estate' => __DIR__ . '/../public',
            ),
        ),
    ),
    'template_placeholders' => array(
        'New Estate ( Mail )' => array(
            '__CODE__' => 'Estate Code',
            '__NAME__' => 'Owner name estate',
        ),
        'Approved Estate ( Mail )' => array(
            '__CODE__' => 'Estate Code',
            '__NAME__' => 'Owner name estate',
            '__VIEWURL__' => 'Link for view approved estate',
        ),
        'View All Info Estate ( Mail )' => array(
            '__TIME__' => 'exp : 15:30:25',
            '__CONTENT__' => 'the default content placeholder'
        ),
        'Send an email to the owner of the estate after purchasing information ( Mail )' => array(
            '__CODE__' => 'Estate Code',
        ),
        'Back from the Banks, especially Home and Show all Info ( Mail )' => array(
            '__PAYERID__' => 'Payment Code',
            '_ESTATEID__' => 'Estate Code',
            '__TIME__' => 'exp : 15:30:25',
        ),
        'New estate & Expire estate $ Confirmation estate ( Sms )' => array(
            '__CODE__' => 'Estate Code',
        ),
        'Verified & not verified estate agent ( Sms )' => array(
            '__USERNAME__' => 'User Name',
            '__USERCODE__' => 'User code',
        ),
    ),

    'fields_entities' => array(
        'real_estate' => 'RealEstate'
    ),
);