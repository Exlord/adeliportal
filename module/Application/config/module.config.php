<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;
$config = array(
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Admin' => 'Application\Controller\AdminController',
            'Application\Controller\Configs' => 'Application\Controller\ConfigsController',
            'Application\Controller\Reports' => 'Application\Controller\ReportsController',
            'Application\Controller\DbBackup' => 'Application\Controller\DbBackupController',
            'Application\Controller\Modules' => 'Application\Controller\ModulesController',
            'Application\Controller\Template' => 'Application\Controller\TemplateController',
            'Application\Controller\Alias' => 'Application\Controller\AliasController',
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'db_backup_table' => 'Application\Model\DbBackupTable',
            'modules_table' => 'Application\Model\ModulesTable',
            'config_table' => 'Application\Model\ConfigTable',
            'template_table' => 'Application\Model\TemplateTable',
            'search_api' => 'Application\API\Search',
            'cache_url_table' => 'Application\Model\CacheUrlTable',
            'alias_url_table' => 'Application\Model\AliasUrlTable',
            'help_api' => 'Application\API\Help',
            'application_event_manager' => 'Application\API\EventManager',
        ),
        'factories' => array(
            'db_backup_api' => 'Application\Factory\DbBackupApi',
            'db_adapter' => 'Application\Factory\DbAdapter',
            'cache' => 'Application\Factory\Cache',
            'memcache' => 'Application\Factory\MemCached',
            'session_manager' => 'Application\Factory\SessionManager',
            'admin_menu' => 'System\Navigation\Service\AdminMenuNavigationFactory',
            'admin_breadcrumbs' => 'System\Navigation\Service\AdminBreadcrumbsNavigationFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/' . __NAMESPACE__ . '.lang',
            ),
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/Zend_Captcha.php',
            ),
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/Zend_Validate.php',
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
//        'layout' => 'layout/layout.phtml',
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'body' => 'Application\View\Helper\Body',
            'Test' => 'Application\View\Helper\Test',
            'marquee_block' => 'Application\View\Helper\MarqueeBlock',
            'custom_block' => 'Application\View\Helper\CustomBlock',
            'search_block' => 'Application\View\Helper\Search',
            'breadcrumb' => 'Application\View\Helper\Breadcrumb',
            'head' => 'Application\View\Helper\Head',
            'has' => 'Application\View\Helper\Has',
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'application' => __DIR__ . '/../public',
            ),
        ),
    ),
    'template_placeholders' => array(
        'Basic' => array(
            '__LONG_DATE__' => 'Long date exp : Tuesday, December 24, 2013',
            '__SHORT_DATE__' => 'Short date exp : 1392/9/12',
            '__TIME__' => 'exp : 15:30:25',
            '__CONTENT__' => 'the default content placeholder',
            '__SITEURL__' => 'site url example : http://ipt24.ir',
            '__SITENAME__' => 'site url example : ipt24.ir or www.ipt24.ir',
        )
    ),
    'components' => array(
        'marquee_block' => array(
            'label' => 'Marquee',
            'description' => 'a simple moving text',
            'helper' => 'marquee_block',
        ),
        'custom_block' => array(
            'label' => 'Custom Html Block',
            'description' => 'create custom html text with a text editor',
            'helper' => 'custom_block',
        ),
        'search_block' => array(
            'label' => 'Search',
            'description' => 'search the site',
            'helper' => 'search_block',
        ),
    ),
);
$config['router'] = array(
    'routes' => array(
        'intro' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/',
                'defaults' => array(
                    'controller' => 'Application\Controller\Index',
                    'action' => 'index',
                ),
            ),
        ),
        'before-update' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/before-update',
                'defaults' => array(
                    'controller' => 'Application\Controller\Index',
                    'action' => 'before-update',
                ),
            ),
        ),
        'app' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '/[:lang]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Index',
                    'action' => 'index',
                ),
                'constraints' => array(
                    'lang' => '[a-z]{0,2}',
                ),
            ),
//                'type' => 'Hostname',
//                'options' => array(
////                    'route' => '[:subdomain.]:domain[.:tld]',
//                    'constraints' => array(),
//                    'defaults' => array(
//                        'subdomain' => '',
//                        'tld' => '',
//                        'controller' => 'Application\Controller\Index',
//                        'action' => 'index',
//                    )
//                ),
            'may_terminate' => true,
            'child_routes' => array(
                'test' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/test',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Index',
                            'action' => 'test',
                        ),
                    ),
                ),
                'updates' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/updates',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Index',
                            'action' => 'updates',
                        ),
                    ),
                ),
                'search' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/search[/:keyword]',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Index',
                            'action' => 'search',
                        ),
                    ),
                ),
                'refresh-captcha' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/refresh-captcha',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Index',
                            'action' => 'refresh-captcha',
                        ),
                    ),
                ),
                'db-backup' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/db-backup',
                        'defaults' => array(
                            'controller' => 'Application\Controller\DbBackup',
                            'action' => 'create',
                        ),
                    ),
                ),
//                    'update' => array(
//                        'type' => 'Segment',
//                        'options' => array(
//                            'route' => '/update[/:hidden-update]',
//                            'defaults' => array(
//                                'controller' => 'Application\Controller\Modules',
//                                'action' => 'update',
//                            ),
//                        ),
//                    ),
                'front-page' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/front',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Index',
                            'action' => 'index',
                        ),
                    ),
                ),
                'print' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/print/:data',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Index',
                            'action' => 'print',
                        ),
                    ),
                )
            ),
        ),
        'admin' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '[/:lang]/admin',
                'defaults' => array(
                    'controller' => 'Application\Controller\Admin',
                    'action' => 'dash-board',
                ),
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'updates' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/updates',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Index',
                            'action' => 'updates',
                        ),
                    ),
                ),
                'help' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/help[/:page]',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Index',
                            'action' => 'help',
                        ),
                    ),
                ),
                'optimization' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/optimization',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Admin',
                            'action' => 'optimization',
                        ),
                    ),
                ),
                'cache' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/cache',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Admin',
                            'action' => 'cache',
                        ),
                    ),
                ),
                'backup' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/backup',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Admin',
                            'action' => 'backup',
                        ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'db' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => '/db',
                                'defaults' => array(
                                    'controller' => 'Application\Controller\DbBackup',
                                    'action' => 'index',
                                ),
                            ),
                            'may_terminate' => true,
                            'child_routes' => array(
                                'unlock' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => '/unlock',
                                        'defaults' => array(
                                            'controller' => 'Application\Controller\DbBackup',
                                            'action' => 'unlock',
                                        ),
                                    ),
                                    'may_terminate' => true,
                                    'child_routes' => array()
                                ),
                                'new' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => '/new',
                                        'defaults' => array(
                                            'controller' => 'Application\Controller\DbBackup',
                                            'action' => 'new',
                                        ),
                                    ),
                                    'may_terminate' => true,
                                    'child_routes' => array()
                                ),
                                'create' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => '/create-backup',
                                        'defaults' => array(
                                            'controller' => 'Application\Controller\DbBackup',
                                            'action' => 'create',
                                        ),
                                    ),
                                    'may_terminate' => true,
                                    'child_routes' => array()
                                ),
                                'restore' => array(
                                    'type' => 'Segment',
                                    'options' => array(
                                        'route' => '/:id/restore',
                                        'defaults' => array(
                                            'controller' => 'Application\Controller\DbBackup',
                                            'action' => 'restore',
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
                                            'controller' => 'Application\Controller\DbBackup',
                                            'action' => 'delete',
                                        ),
                                    ),
                                    'may_terminate' => true,
                                    'child_routes' => array()
                                ),
                                'download' => array(
                                    'type' => 'Segment',
                                    'options' => array(
                                        'route' => '/download/:file',
                                        'defaults' => array(
                                            'controller' => 'Application\Controller\DbBackup',
                                            'action' => 'download',
                                        ),
                                    ),
                                    'may_terminate' => true,
                                    'child_routes' => array()
                                ),
                            )
                        ),
                    )
                ),
                'widget-loader' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/widget-loader',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Admin',
                            'action' => 'widget-loader',
                        ),
                    ),
                ),
                'configs' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/configs',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Admin',
                            'action' => 'configs',
                        ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'system' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => '/system',
                                'defaults' => array(
                                    'controller' => 'Application\Controller\Admin',
                                    'action' => 'config',
                                ),
                            ),
                            'may_terminate' => true,
                            'child_routes' => array(
                                'delete-fav-icon' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => '/delete-fav-icon',
                                        'defaults' => array(
                                            'controller' => 'Application\Controller\Admin',
                                            'action' => 'delete-fav-icon',
                                        ),
                                    ),
                                ),
                                'delete-admin-logo' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => '/delete-admin-logo',
                                        'defaults' => array(
                                            'controller' => 'Application\Controller\Admin',
                                            'action' => 'delete-admin-logo',
                                        ),
                                    ),
                                ),
                            )
                        ),
                        'widgets' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => '/widgets',
                                'defaults' => array(
                                    'controller' => 'Application\Controller\Admin',
                                    'action' => 'widgets',
                                ),
                            ),
                        ),
                    )
                ),
                'contents' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/contents',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Admin',
                            'action' => 'contents',
                        ),
                    )
                ),
                'modules' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/modules',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Modules',
                            'action' => 'index',
                        ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'rebuild' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => '/rebuild',
                                'defaults' => array(
                                    'controller' => 'Application\Controller\Modules',
                                    'action' => 'rebuild',
                                ),
                            ),
                        ),
                    )
                ),
                'structure' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/structure',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Admin',
                            'action' => 'structure',
                        ),
                    )
                ),
                'orders' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/orders',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Admin',
                            'action' => 'orders',
                        ),
                    )
                ),
                'reports' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/reports',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Reports',
                            'action' => 'index',
                        ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(),
                ),
                'template' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/template',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Template',
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
                                    'controller' => 'Application\Controller\Template',
                                    'action' => 'new',
                                ),
                            ),
                        ),
                        'edit' => array(
                            'type' => 'Segment',
                            'options' => array(
                                'route' => '/:id/edit',
                                'defaults' => array(
                                    'controller' => 'Application\Controller\Template',
                                    'action' => 'edit',
                                ),
                            ),
                        ),
                        'delete' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => '/delete',
                                'defaults' => array(
                                    'controller' => 'Application\Controller\Template',
                                    'action' => 'delete',
                                ),
                            ),
                        ),
                    )
                ),
                'alias' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/alias',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Alias',
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
                                    'controller' => 'Application\Controller\Alias',
                                    'action' => 'new',
                                ),
                            ),
                        ),
                        'edit' => array(
                            'type' => 'Segment',
                            'options' => array(
                                'route' => '/:id/edit',
                                'defaults' => array(
                                    'controller' => 'Application\Controller\Alias',
                                    'action' => 'edit',
                                ),
                            ),
                        ),
                        'delete' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => '/delete',
                                'defaults' => array(
                                    'controller' => 'Application\Controller\Alias',
                                    'action' => 'delete',
                                ),
                            ),
                        ),
                    )
                ),
            ),
        ),
    ),
);
$config['navigation'] = array(
    'admin_menu' => array(
        'admin' => array(
            'label' => 'Administration',
            'route' => 'admin',
            'resource' => 'root:admin:menu',
            'order' => -9999,
            'pages' => array(
                array(
                    'label' => 'Dashboard',
                    'route' => 'admin',
                    'resource' => 'route:admin',
                ),
                array(
                    'label' => 'Updates',
                    'route' => 'admin/updates',
//                    'resource' => 'route:admin/updates',
                ),
                array(
                    'label' => 'Optimization',
                    'route' => 'admin/optimization',
                    'resource' => 'route:admin/optimization',
                ),
                array(
                    'label' => 'Clear Cache',
                    'route' => 'admin/cache',
                    'resource' => 'route:admin/cache',
                ),
                array(
                    'label' => 'Backup',
                    'route' => 'admin/backup',
                    'resource' => 'route:admin/backup',
                    'pages' => array(
                        array(
                            'label' => 'DataBase Backup',
                            'route' => 'admin/backup/db',
                            'resource' => 'route:admin/backup/db',
                            'pages' => array(
                                array(
                                    'label' => 'New DataBase Backup',
                                    'route' => 'admin/backup/db/new',
                                    'resource' => 'route:admin/backup/db/new',
                                    'pages' => array()
                                )
                            )
                        )
                    )
                ),
                array(
                    'label' => 'Templates',
                    'route' => 'admin/template',
                    'resource' => 'route:admin/template',
                    'pages' => array(
                        array(
                            'label' => 'New Template',
                            'route' => 'admin/template/new',
                            'resource' => 'route:admin/template/new',
                        ),
                        array(
                            'label' => 'Edit Template',
                            'route' => 'admin/template/edit',
                            'resource' => 'route:admin/template/edit',
                            'visible' => false
                        ),
                        array(
                            'label' => 'Delete Template',
                            'route' => 'admin/template/delete',
                            'resource' => 'route:admin/template/delete',
                            'visible' => false
                        ),
                    )
                ),
                array(
                    'label' => 'Help',
                    'route' => 'admin/help',
                    'order' => 10000,
//            'resource' => 'route:admin/help',
                ),
            ),
        ),
        'configs' => array(
            'label' => 'Configs',
            'route' => 'admin/configs',
            'resource' => 'route:admin/configs',
            'order' => -9998,
            'pages' => array(
                array(
                    'label' => 'System',
                    'route' => 'admin/configs/system',
                    'resource' => 'route:admin/configs/system',
                    'note' => 'Change default templates and system default route',
                ),
                array(
                    'label' => 'Dashboard Widgets',
                    'route' => 'admin/configs/widgets',
                    'resource' => 'route:admin/configs/widgets',
                ),
            ),
        ),
        'contents' => array(
            'label' => 'Contents',
            'route' => 'admin/contents',
            'order' => -9997,
            'resource' => 'route:admin/contents'
        ),
        'modules' => array(
            'label' => 'Modules',
            'route' => 'admin/modules',
            'order' => -9996,
            'resource' => 'route:admin/modules',
            'pages' => array(
                array(
                    'label' => 'Alias Url',
                    'route' => 'admin/alias',
                    'resource' => 'route:admin/alias',
                    'pages' => array(
                        array(
                            'label' => 'New Alias Url',
                            'route' => 'admin/alias/new',
                            'resource' => 'route:admin/alias/new',
                        ),
                        array(
                            'label' => 'Edit Alias Url',
                            'route' => 'admin/alias/edit',
                            'resource' => 'route:admin/alias/edit',
                            'visible' => false
                        ),
                    )
                ),
            ),
        ),
        'structure' => array(
            'label' => 'Structure',
            'route' => 'admin/structure',
            'order' => -9995,
            'resource' => 'route:admin/structure',
            'pages' => array(),
        ),

        'reports' => array(
            'label' => 'Reports',
            'route' => 'admin/reports',
            'resource' => 'route:admin/reports',
            'order' => 9999,
            'pages' => array()
        ),
    ),
);
//if (IS_DEVELOPMENT_SERVER)
$config['view_manager']['display_exceptions'] = true;

return $config;