<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Rating;
return array(
    'service_manager' => array(
        'invokables' => array(
            'rating_table' => 'Rating\Model\RatingTable',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Rating\Controller\RatingAdmin' => 'Rating\Controller\RatingAdminController',
            'Rating\Controller\Rating' => 'Rating\Controller\RatingController',
            'Rating\Controller\NegativePositiveRate' => 'Rating\Controller\NegativePositiveRateController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'rating' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/rating',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Rating\Controller\Rating',
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
                                         'controller' => 'Rating\Controller\Rating',
                                         'action' => 'new',
                                     ),
                                 ),
                             ),
                         ),
                    ),
                    'negative-positive-rate' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/negative-positive-rate',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Rating\Controller\NegativePositiveRate',
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
                                        'controller' => 'Rating\Controller\NegativePositiveRate',
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'rating' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/rating',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Rating\Controller\RatingAdmin',
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
                                        'controller' => 'Rating\Controller\RatingAdmin',
                                        'action' => 'config',
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
            'configs'=>array(
                'pages' => array(
                    array(
                        'label' => 'Rating',
                        'route' => 'admin/rating/config',
                        'resource' => 'route:admin/rating/config',
                    ),
                ),
            )
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'rating' => 'Rating\View\Helper\Rating',
            'rating_average' => 'Rating\View\Helper\RatingAverage',
            'negative_positive' => 'Rating\View\Helper\NegativeAndPositiveRate',
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
                'Rating' => __DIR__ . '/../public',
            ),
        ),
    ),
    'Rating' => array(
        'name' => 'Rating Module',
        'note' => 'A Rating Module',
        'can_be_disabled' => true,
        'client_usable' => true,
        'depends' => array('System'),
        'stage' => 'development','version'=>'1.0'
    ),
);