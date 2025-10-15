<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace File;
return array(
    'controller_plugins' => array(
        'invokables' => array(
            'stream' => 'HumusStreamResponseSender\Controller\Plugin\Stream'
        )
    ),
    'service_manager' => array(
        'invokables' => array(
            'file_table' => 'File\Model\FileTable',
            'file_api' => 'File\API\File',
            'private_file_table' => 'File\Model\PFileTable',
            'private_file_usage' => 'File\Model\PFileUsage',
        ),
        'factories' => array(
            'HumusStreamResponseSender\StreamResponseSender' => 'HumusStreamResponseSender\StreamResponseSenderFactory'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'File\Controller\File' => 'File\Controller\FileController',
            'File\Controller\PrivateFile' => 'File\Controller\PrivateFile',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'delete-file' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/delete-file/file=[:file]',
                            'defaults' => array(
                                'controller' => 'File\Controller\File',
                                'action' => 'delete-file',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'pf-ddl' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/pf/ddl/:file',
                            'defaults' => array(
                                'controller' => 'File\Controller\PrivateFile',
                                'action' => 'direct-download',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'pf-r' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/pf/r/:file',
                            'defaults' => array(
                                'controller' => 'File\Controller\PrivateFile',
                                'action' => 'read',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                )
            ),
            'admin' => array(
                'child_routes' => array(
                    'file-types' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/file-types/:type',
                            'defaults' => array(
                                'controller' => 'File\Controller\File',
                                'action' => 'file-types',
                            ),
                        ),
                    ),
                    'file' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/file',
                            'defaults' => array(
                                'controller' => 'File\Controller\File',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'connector' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/connector',
                                    'defaults' => array(
                                        'controller' => 'File\Controller\File',
                                        'action' => 'connector',
                                        'type' => 'public'
                                    ),
                                ),
                            ),
                            'public' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/public',
                                    'defaults' => array(
                                        'controller' => 'File\Controller\File',
                                        'action' => 'public',
                                    ),
                                ),
                            ),
                            'public-manager' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/public-manager',
                                    'defaults' => array(
                                        'controller' => 'File\Controller\File',
                                        'action' => 'public-manager',
                                    ),
                                ),
                            ),
                            'private' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/private',
                                    'defaults' => array(
                                        'controller' => 'File\Controller\PrivateFile',
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
                                                'controller' => 'File\Controller\PrivateFile',
                                                'action' => 'new',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'File\Controller\PrivateFile',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'File\Controller\PrivateFile',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'File\Controller\File',
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
                'pages' => array(
                    array(
                        'label' => 'Files',
                        'route' => 'admin/file',
                        'resource' => 'route:admin/file',
                        'pages' => array(
                            array(
                                'label' => 'Public Files',
                                'route' => 'admin/file/public',
                                'resource' => 'route:admin/file/public',
                            ),
                            array(
                                'label' => 'Private Files',
                                'route' => 'admin/file/private',
                                'resource' => 'route:admin/file/private',
                                'pages' => array(
                                    array(
                                        'label' => 'New',
                                        'route' => 'admin/file/private/new',
                                        'resource' => 'route:admin/file/private/new',
                                        'pages' => array()
                                    ),
                                    array(
                                        'label' => 'Edit',
                                        'route' => 'admin/file/private/edit',
                                        'resource' => 'route:admin/file/private/edit',
                                        'visible' => false
                                    )
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
    'view_helpers' => array(
        'invokables' => array(
            'fileCollection' => 'File\View\Helper\FileCollection',
            'fileDisplay' => 'File\View\Helper\FileDisplay',
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
                'file' => __DIR__ . '/../public',
            ),
        ),
    ),
    'HumusStreamResponseSender' => array(
        'enable_speed_limit' => true,
        'enable_range_support' => true,
        'chunk_size' => 1024 * 1024 //  = 1MB/s
    ),
    'private_file' => array(
        'max_upload_size' => '1MB',
        'extensions' => array('jpg', 'jpeg', 'pjpeg', 'gif', 'png', 'bmp', 'psd', 'zip', 'gz', 'tar.gz', 'rar', '7z',
            'pdf',
            'csv', 'txt', 'rtf', 'doc', 'docx', 'mkv', 'webm', 'xvid', 'divx', 'mpeg', 'mp4', 'mp3', 'avi', 'ogg',
            'flv', 'wmv'),
        'mime_types' => array('application/pdf', 'application/zip', 'application/gzip', 'application/x-7z-compressed',
            'image', 'audio', 'text/csv',
            'text/plain', 'text/rtf', 'video')
    )
);