<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Ads;
return array(
    'service_manager' => array(
        'invokables' => array(
            'ads_table' => 'Ads\Model\AdsTable',
            'ads_order_table' => 'Ads\Model\AdsOrderTable',
            'ads_ref_table' => 'Ads\Model\AdsRefTable',
            'ads_api' => 'Ads\API\Ads',
            'ads_event_manager' => 'Ads\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Ads\Controller\Admin' => 'Ads\Controller\AdminController',
            'Ads\Controller\Client' => 'Ads\Controller\ClientController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'ad' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ad',
                            'defaults' => array(
                                'controller' => 'Ads\Controller\Client',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'view' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:baseType/view/:adId[/:adTitle]',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Client',
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                            'base' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:baseType',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Client',
                                        'action' => 'base',
                                    ),
                                ),
                            ),
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:baseType/list[/:isRequest[/:baseTitle]]',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Client',
                                        'action' => 'list',
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/new[/:baseType[/:isRequest]]',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'new-validate' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/new-validate/:params/:paymentId',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Client',
                                        'action' => 'new-validate',
                                    ),
                                ),
                            ),
                            'view-data-validate' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/view-data-validate/:params/:paymentId',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Client',
                                        'action' => 'view-data-validate',
                                    ),
                                ),
                            ),
                            'search' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:baseType/search',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Client',
                                        'action' => 'search',
                                    ),
                                ),
                            ),
                        )
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'ad' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ad',
                            'defaults' => array(
                                'controller' => 'Ads\Controller\Admin',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'ref' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/ref',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'new-ads-ref',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'new' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/new/:adId',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'new-ads-ref',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete/:adId',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'remove-ads-ref',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:baseType',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'list',
                                    ),
                                ),
                            ),
                            'get-data-block' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/get-data',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'get-data-block',
                                    ),
                                ),
                            ),
                            'upgrade' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/upgrade',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'upgrade',
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/new[/:baseType[/:isRequest]]',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'request-search-count' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/request-search-count',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'request-search-count',
                                    ),
                                ),
                            ),
                            'delete-img' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete-img',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'delete-img',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'Ads\Controller\Admin',
                                        'action' => 'config',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'new-type' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/create',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'new-type-config',
                                            ),
                                        ),
                                    ),
                                    'first-config' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/first[/:baseType[/:isRequest]]',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'first-config',
                                            ),
                                        ),
                                    ),
                                    'second-config' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/second[/:baseType[/:isRequest]]',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'second-config',
                                            ),
                                        ),
                                    ),
                                    'third-config' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/third[/:baseType[/:isRequest]]',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'third-config',
                                            ),
                                        ),
                                    ),
                                    'four-config' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/four[/:baseType[/:isRequest]]',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'four-config',
                                            ),
                                        ),
                                    ),
                                    'advance-config' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/advance',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'advance-config',
                                            ),
                                        ),
                                    ),
                                    'select-fields' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/select-fields[/:baseType[/:isRequest]]',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'select-fields-config',
                                            ),
                                        ),
                                    ),
                                    'filter-fields' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/filter-fields[/:baseType[/:isRequest]]',
                                            'defaults' => array(
                                                'controller' => 'Ads\Controller\Admin',
                                                'action' => 'filter-fields-config',
                                            ),
                                        ),
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
                        'label' => 'ADS_AD',
                        'route' => 'admin/ad/config',
                        'resource' => 'route:admin/ad/config',
                        'pages' => array(
                            array(
                                'label' => 'ADS_CREATE_NEW_AD_TYPE',
                                'route' => 'admin/ad/config/new-type',
                                'resource' => 'route:admin/ad/config/new-type',
                            ),
                            array(
                                'label' => 'ADS_FIRST_STAGE',
                                'route' => 'admin/ad/config/first-config',
                                'resource' => 'route:admin/ad/config/first-config',
                            ),
                            array(
                                'label' => 'ADS_SECOND_STAGE',
                                'route' => 'admin/ad/config/second-config',
                                'resource' => 'route:admin/ad/config/second-config',
                            ),
                            array(
                                'label' => 'ADS_THIRD_STAGE',
                                'route' => 'admin/ad/config/third-config',
                                'resource' => 'route:admin/ad/config/third-config',
                            ),
                            array(
                                'label' => 'ADS_FOUR_STAGE',
                                'route' => 'admin/ad/config/four-config',
                                'resource' => 'route:admin/ad/config/four-config',
                            ),
                            array(
                                'label' => 'ADS_ADVANCE_CONFIGS',
                                'route' => 'admin/ad/config/advance-config',
                                'resource' => 'route:admin/ad/config/advance-config',
                            ),
                            array(
                                'label' => 'ADS_SELECT_FIELDS',
                                'route' => 'admin/ad/config/select-fields',
                                'resource' => 'route:admin/ad/config/select-fields',
                            ),
                            array(
                                'label' => 'ADS_FILTER_FIELDS',
                                'route' => 'admin/ad/config/filter-fields',
                                'resource' => 'route:admin/ad/config/filter-fields',
                            ),

                        )
                    )
                ),
            ),
            'modules' => array(
                'pages' => array(
                    array(
                        'label' => 'ADS_AD',
                        'route' => 'admin/ad',
                        'resource' => 'route:admin/ad',
                        /*'pages' => array(
                            array(
                                'label' => 'New',
                                'route' => 'admin/ad/new',
                                'resource' => 'route:admin/ad/new',
                            ),
                        )*/
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
            'ads_search_block' => 'Ads\View\Helper\Search',
            'ads_categories_block' => 'Ads\View\Helper\Categories',
            'ads_block' => 'Ads\View\Helper\AdsBlock',
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
                'Ads' => __DIR__ . '/../public',
            ),
        ),
    ),
    'components' => array(
        'ads_block' => array(
            'label' => 'ADS_AD',
            'description' => '',
            'helper' => 'ads_block',
        )
    ),
    'template_placeholders' => array(
        'ADS_NEW Mail & SMS' => array(
            '__AD_CODE__' => 'ADS_CODE',
            '__NAME__' => 'Name',
            '__SITE_URL__' => 'Site Url',
            '__VIEW_LINK__' => 'View Link',
            '__USERNAME__' => 'Username',
            '__PASSWORD__' => 'Password',
        ),
        'ADS_APPROVED Mail & SMS' => array(
            '__AD_CODE__' => 'ADS_CODE',
            '__NAME__' => 'Name',
            '__SITE_URL__' => 'Site Url',
            '__VIEW_LINK__' => 'View Link',
        ),
        'ADS_NEW_VALIDATE Mail & SMS' => array(
            '__AD_CODE__' => 'ADS_CODE',
            '__PAYER_CODE__' => 'Payer Code',
            '__SITE_URL__' => 'Site Url',
        ),
        'ADS_WILL_EXPIRE Mail & SMS' => array(
            '__AD_CODE__' => 'ADS_CODE',
            '__NAME__' => 'Name',
            '__SITE_URL__' => 'Site Url',
            '__VIEW_LINK__' => 'View Link',
            '__ADMIN_LINK__' => 'Admin Link',
        ),
        'ADS_EXPIRED Mail & SMS' => array(
            '__AD_CODE__' => 'ADS_CODE',
            '__NAME__' => 'Name',
            '__SITE_URL__' => 'Site Url',
            '__VIEW_LINK__' => 'View Link',
            '__ADMIN_LINK__' => 'Admin Link',
        ),
    ),
);