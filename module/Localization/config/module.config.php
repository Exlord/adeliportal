<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Localization;
return array(
    'service_manager' => array(
        'invokables' => array(
            'language_table' => 'Localization\Model\LanguageTable',
            'translate_table' => 'Localization\Model\TranslationTable',
            'translation_api' => 'Localization\API\Translation',
            'language_content_table' => 'Localization\Model\LanguageContentTable',
            'language_event_manager' => 'Localization\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Localization\Controller\Language' => 'Localization\Controller\LanguageController',
            'Localization\Controller\Translation' => 'Localization\Controller\TranslationController',
            'Localization\Controller\Localization' => 'Localization\Controller\Localization',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'translation' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/translation',
                            'defaults' => array(
                                'controller' => 'Localization\Controller\Translation',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'select-entity' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:entityType',
                                    'defaults' => array(
                                        'controller' => 'Localization\Controller\Translation',
                                        'action' => 'select-entity',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'translate' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:language/:id/translate',
                                            'defaults' => array(
                                                'controller' => 'Localization\Controller\Translation',
                                                'action' => 'translate',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'translate-miscellaneous' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/translate-miscellaneous',
                            'defaults' => array(
                                'controller' => 'Localization\Controller\Translation',
                                'action' => 'miscellaneous',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/add',
                                    'defaults' => array(
                                        'controller' => 'Localization\Controller\Translation',
                                        'action' => 'add',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:key/edit',
                                    'defaults' => array(
                                        'controller' => 'Localization\Controller\Translation',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                        )
                    ),
                    'languages' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/languages',
                            'defaults' => array(
                                'controller' => 'Localization\Controller\Language',
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
                                        'controller' => 'Localization\Controller\Language',
                                        'action' => 'update',
                                    ),
                                ),
                            ),
                        )
                    ),
                    'localization-config' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/localization-config',
                            'defaults' => array(
                                'controller' => 'Localization\Controller\Localization',
                                'action' => 'config',
                            ),
                        ),
                    ),
                ),
            )
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'PERM_Localization',
                        'route' => 'admin/localization-config',
                        'resource' => 'route:admin/localization-config',
                    )
                )
            ),
            'structure' => array(
                'pages' => array(
                    array(
                        'label' => 'Languages',
                        'route' => 'admin/languages',
                        'resource' => 'route:admin/languages',
                    )
                ),
            ),
            'modules' => array(
                'pages' => array(
                    array(
                        'label' => 'Content Translation',
                        'route' => 'admin/translation',
                        'resource' => 'route:admin/translation',
                        'order' => 9999,
                        'pages' => array(
                            array(
                                'label' => 'Entities',
                                'route' => 'admin/translation/select-entity',
//                                'resource' => 'route:admin/translation/select-entity',
                                'visible' => false,
                                'pages' => array(
                                    array(
                                        'label' => 'Translate',
                                        'route' => 'admin/translation/select-entity/translate',
//                                        'resource' => 'route:admin/translation/select-entity/translate',
                                        'visible' => false,
                                    )
                                )
                            ),
                            array(
                                'label' => 'Miscellaneous',
                                'route' => 'admin/translate-miscellaneous',
                                'resource' => 'route:admin/translate-miscellaneous',
                                'pages' => array(
                                    array(
                                        'label' => 'New',
                                        'route' => 'admin/translate-miscellaneous/add',
                                        'resource' => 'route:admin/translate-miscellaneous/add',
                                    ),
                                    array(
                                        'label' => 'Edit',
                                        'route' => 'admin/translate-miscellaneous/edit',
                                        'resource' => 'route:admin/translate-miscellaneous/edit',
                                        'visible' => false,
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
            'langSwitcher' => 'Localization\View\Helper\LanguageSwitcher',
            'dateFormat' => 'Localization\View\Helper\DateFormat'
        ),
        'factories' => array(
            'currencyFormat' => 'Localization\Factory\CurrencyFormat',
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/' . __NAMESPACE__ . '.lang',
            ),
//            array(
//                'type' => 'phpArray',
//                'base_dir' => __DIR__ . '/../../../language',
//                'pattern' => '%s/miscellaneous.lang',
//            ),
        ),
    ),
    'components' => array(
        'language_switcher_block' => array(
            'label' => 'Language Switcher',
            'description' => 'Multi language in your website',
            'helper' => 'langSwitcher',
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'localization' => __DIR__ . '/../public',
            ),
        ),
    ),
);