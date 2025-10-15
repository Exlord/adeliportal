<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Gallery;
$configs = array(
    'service_manager' => array(
        'invokables' => array(
            // Keys are the service names
            // Values are valid class names to instantiate.
            'gallery_table' => 'Gallery\Model\GalleryTable',
            'gallery_item_table' => 'Gallery\Model\GalleryItemTable',
            'order_banner_table' => 'Gallery\Model\OrderBannerTable',
            'banner_table' => 'Gallery\Model\BannerTable',
            'banner_size_table' => 'Gallery\Model\BannerSizeTable',
            'gallery_api' => 'Gallery\API\Gallery',
            'gallery_event_manager' => 'Gallery\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Gallery\Controller\Gallery' => 'Gallery\Controller\GalleryController',
            'Gallery\Controller\GalleryPage' => 'Gallery\Controller\GalleryPageController',
            'Gallery\Controller\GalleryItem' => 'Gallery\Controller\GalleryItemController',
            'Gallery\Controller\BannerSize' => 'Gallery\Controller\BannerSizeController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'banner-loader' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/banner-loader',
                            'defaults' => array(
                                'controller' => 'Gallery\Controller\GalleryItem',
                                'action' => 'banner-loader',
                            ),
                        ),
                    ),
                    'gallery-counter' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/gallery-counter[/:id][/:hitsType]',
                            'defaults' => array(
                                'controller' => 'Gallery\Controller\GalleryItem',
                                'action' => 'gallery-counter',
                            ),
                        ),
                    ),
                    'order-banner' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/order-banner',
                            'defaults' => array(
                                'controller' => 'Gallery\Controller\Gallery',
                                'action' => 'order-banner',
                            ),
                        ),
                    ),
                    'photo-galleries' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/photo-galleries',
                            'defaults' => array(
                                'controller' => 'Gallery\Controller\GalleryPage',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'photo-gallery' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/photo-gallery/:id[/:title]',
                            'defaults' => array(
                                'controller' => 'Gallery\Controller\GalleryPage',
                                'action' => 'photo-gallery',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'navigation' => array(
        'admin_menu' => array(
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Banner Configs',
                        'route' => 'admin/banner/configs',
                        'resource' => 'route:admin/banner/configs',
                    ),
                    array(
                        'label' => 'Gallery',
                        'route' => 'admin/gallery/configs',
                        'resource' => 'route:admin/gallery/configs',
                    )
                )
            ),
            'orders' => array(
                'label' => 'Orders',
                'route' => 'admin/orders',
                'order' => -9995,
                'resource' => 'route:admin/orders',
                'pages' => array(
                    array(
                        'label' => 'Orders Banner List',
                        'route' => 'admin/banner/list',
                        'resource' => 'route:admin/banner/list',
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
    'view_helpers' => array(
        'invokables' => array(
            'gallery_block' => 'Gallery\View\Helper\GalleryBlock',
            'banner_block' => 'Gallery\View\Helper\GalleryBlock',
            'slider_block' => 'Gallery\View\Helper\GalleryBlock',
            'image_box_block' => 'Gallery\View\Helper\GalleryBlock',
            'gallery_widget' => 'Gallery\View\Helper\Widget',
            'photo_gallery' => 'Gallery\View\Helper\PhotoGallery',
        ),
    ),
    'widgets' => array(
        'Gallery\View\Helper\Widget' => 'Gallery Widget'
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
                'Gallery' => __DIR__ . '/../public',
            ),
        ),
    ),
    'components' => array(
        'gallery_block' => array(
            'label' => 'Gallery',
            'description' => '',
            'helper' => 'gallery_block',
        ),
        'banner_block' => array(
            'label' => 'Banner',
            'description' => '',
            'helper' => 'banner_block',
        ),
        'slider_block' => array(
            'label' => 'Slider',
            'description' => '',
            'helper' => 'slider_block',
        ),
        'image_box_block' => array(
            'label' => 'Image Box',
            'description' => '',
            'helper' => 'image_box_block',
        )
    ),

    'template_placeholders' => array(
        'Banner Expired ( sms )' => array(
            '__BANNER_CODE__' => 'Banner Code',
        ),
    ),

);


$name = array('Banner' => 'banner', 'Slider' => 'slider', 'Photo Gallery' => 'gallery', 'Image Box' => 'imageBox');
$route = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '',
        'defaults' => array(
            'controller' => 'Gallery\Controller\Gallery',
            'action' => 'index',
        ),
    ),
    'may_terminate' => true,
    'child_routes' => array(
        'groups' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/groups',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\Gallery',
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
                            'controller' => 'Gallery\Controller\Gallery',
                            'action' => 'new',
                        ),
                    ),
                ),
                'edit' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/:id/edit',
                        'defaults' => array(
                            'controller' => 'Gallery\Controller\Gallery',
                            'action' => 'edit',
                        ),
                    ),
                ),
                'delete' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/delete',
                        'defaults' => array(
                            'controller' => 'Gallery\Controller\Gallery',
                            'action' => 'delete',
                        ),
                    ),
                ),
                'update' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/update',
                        'defaults' => array(
                            'controller' => 'Gallery\Controller\Gallery',
                            'action' => 'update',
                        ),
                    ),
                ),
                'delete-img' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/delete-img',
                        'defaults' => array(
                            'controller' => 'Gallery\Controller\Gallery',
                            'action' => 'delete-img',
                        ),
                    ),
                ),
            ),
        ),
        'item' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/item',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\GalleryItem',
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
                            'controller' => 'Gallery\Controller\GalleryItem',
                            'action' => 'new',
                        ),
                    ),
                ),
                'edit' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/:id/edit',
                        'defaults' => array(
                            'controller' => 'Gallery\Controller\GalleryItem',
                            'action' => 'edit',
                        ),
                    ),
                ),
                'delete' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/delete',
                        'defaults' => array(
                            'controller' => 'Gallery\Controller\GalleryItem',
                            'action' => 'delete',
                        ),
                    ),
                ),
                'update' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/update',
                        'defaults' => array(
                            'controller' => 'Gallery\Controller\GalleryItem',
                            'action' => 'update',
                        ),
                    ),
                ),
                'delete-img' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/delete-img',
                        'defaults' => array(
                            'controller' => 'Gallery\Controller\GalleryItem',
                            'action' => 'delete-img',
                        ),
                    ),
                ),
            )
        ),

    ),
);

foreach ($name as $label => $type) {
    $configs['navigation']['admin_menu']['contents']['pages'][] = \Gallery\API\Gallery::getNavigation($label, $type);

    $route['options']['route'] = '/' . $type;
    $route['options']['defaults']['type'] = $type;
    $configs['router']['routes']['admin']['child_routes'][$type] = $route;
}
$configs['router']['routes']['admin']['child_routes']['banner']['child_routes']['configs'] = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '/configs',
        'defaults' => array(
            'controller' => 'Gallery\Controller\Gallery',
            'action' => 'banner-config',
        ),
    ),
);

$configs['router']['routes']['admin']['child_routes']['banner']['child_routes']['extension'] = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '/extension',
        'defaults' => array(
            'controller' => 'Gallery\Controller\Gallery',
            'action' => 'extension',
        ),
    ),
);
$configs['router']['routes']['admin']['child_routes']['banner']['child_routes']['list'] = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '/list',
        'defaults' => array(
            'controller' => 'Gallery\Controller\Gallery',
            'action' => 'order-banner-list',
        ),
    ),
    'may_terminate' => true,
    'child_routes' => array(
        /*'new' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/new',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\Gallery',
                    'action' => 'new',

                ),
            ),
        ),*/
        'edit' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '/:id/edit',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\Gallery',
                    'action' => 'order-banner-edit',

                ),
            ),
        ),
        'delete' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/delete',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\Gallery',
                    'action' => 'order-banner-delete',

                ),
            ),
        ),
        'delete-image' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/delete-image',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\Gallery',
                    'action' => 'order-banner-delete-image',

                ),
            ),
        ),
        'update' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/update',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\Gallery',
                    'action' => 'order-banner-update',
                ),
            ),
        ),
    ),
);

$configs['router']['routes']['admin']['child_routes']['banner']['child_routes']['size'] = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '/size',
        'defaults' => array(
            'controller' => 'Gallery\Controller\BannerSize',
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
                    'controller' => 'Gallery\Controller\BannerSize',
                    'action' => 'new',

                ),
            ),
        ),
        'edit' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '/:id/edit',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\BannerSize',
                    'action' => 'edit',

                ),
            ),
        ),
        'delete' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/delete',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\BannerSize',
                    'action' => 'delete',

                ),
            ),
        ),
        'update' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/update',
                'defaults' => array(
                    'controller' => 'Gallery\Controller\BannerSize',
                    'action' => 'update',
                ),
            ),
        ),
    ),
);

$configs['router']['routes']['admin']['child_routes']['gallery']['child_routes']['configs'] = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '/configs',
        'defaults' => array(
            'controller' => 'Gallery\Controller\GalleryPage',
            'action' => 'config',
        ),
    ),
);
$configs['router']['routes']['admin']['child_routes']['gallery']['child_routes']['gallery-page-list'] = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '/gallery-page-list',
        'defaults' => array(
            'controller' => 'Gallery\Controller\Gallery',
            'action' => 'gallery-page-list',
        ),
    ),
);

return $configs;



