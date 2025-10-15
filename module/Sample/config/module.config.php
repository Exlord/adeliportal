<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Sample;
return array(
    'service_manager' => array(
        'abstract_factories' => array(
            // Valid values include names of classes implementing
            // AbstractFactoryInterface, instances of classes implementing
            // AbstractFactoryInterface, or any PHP callbacks
            'SomeModule\Service\FallbackFactory',
        ),
        'aliases' => array(
            // Aliasing a FQCN to a service name
            'SomeModule\Model\User' => 'User',
            // Aliasing a name to a known service name
            'AdminUser' => 'User',
            // Aliasing to an alias
            'SuperUser' => 'AdminUser',
        ),
        'factories' => array(
            // Keys are the service names.
            // Valid values include names of classes implementing
            // FactoryInterface, instances of classes implementing
            // FactoryInterface, or any PHP callbacks
            'User' => 'SomeModule\Service\UserFactory',
        ),
        'invokables' => array(
            // Keys are the service names
            // Values are valid class names to instantiate.
            'UserInputFiler' => 'SomeModule\InputFilter\User',
        ),
        'shared' => array(
            // Usually, you'll only indicate services that should _NOT_ be
            // shared -- i.e., ones where you want a different instance
            // every time.
            'UserForm' => false,
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Sample\Controller\Sample' => 'Sample\Controller\SampleController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'Sample' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/Sample',
                            'defaults' => array(
                                'controller' => 'Sample\Controller\Sample',
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
                                        'controller' => 'Sample\Controller\Sample',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id/edit',
                                    'defaults' => array(
                                        'controller' => 'Sample\Controller\Sample',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/delete',
                                    'defaults' => array(
                                        'controller' => 'Sample\Controller\Sample',
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update',
                                    'defaults' => array(
                                        'controller' => 'Sample\Controller\Sample',
                                        'action' => 'update',
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
                        'label' => 'Sample',
                        'route' => 'admin/Sample',
                        'resource' => 'route:admin/Sample',
                        'pages' => array(
                            array(
                                'label' => 'New',
                                'route' => 'admin/Sample/new',
                            ),
                            array(
                                'route' => 'admin/Sample/edit',
                                'visible' => false
                            ),
                            array(
                                'route' => 'admin/Sample/delete',
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
        'invokables' => array(
            'sample_helper' => 'Sample\View\Helper\Sample',
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
                'Sample' => __DIR__ . '/../public',
            ),
        ),
    ),
);