<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace EducationalCenter;
return array(
    'service_manager' => array(
        'invokables' => array(
            'ec_workshop_table' => 'EducationalCenter\Model\WorkshopTable',
            'ec_workshop_timetable' => 'EducationalCenter\Model\WorkshopTimeTable',
            'ec_workshop_class_table' => 'EducationalCenter\Model\WorkshopClassTable',
            'ec_workshop_attendance_table' => 'EducationalCenter\Model\WorkshopAttendanceTable',
            'ec_api' => 'EducationalCenter\API\EC',
            'ec_event_manager' => 'EducationalCenter\API\EventManager'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'EducationalCenter\Controller\WorkshopAdmin' => 'EducationalCenter\Controller\WorkshopAdmin',
            'EducationalCenter\Controller\WorkshopTime' => 'EducationalCenter\Controller\WorkshopTime',
            'EducationalCenter\Controller\Workshop' => 'EducationalCenter\Controller\Workshop',
            'EducationalCenter\Controller\WorkshopClass' => 'EducationalCenter\Controller\WorkshopClass',
            'EducationalCenter\Controller\EducationalCenter' => 'EducationalCenter\Controller\EducationalCenter',
            'EducationalCenter\Controller\WorkshopAttendance' => 'EducationalCenter\Controller\WorkshopAttendance',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'workshops' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/workshops',
                            'defaults' => array(
                                'controller' => 'EducationalCenter\Controller\Workshop',
                                'action' => 'workshops',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'finalize-payment' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/finalize-payment/:params/:paymentId',
                                    'defaults' => array(
                                        'controller' => 'EducationalCenter\Controller\Workshop',
                                        'action' => 'finalize-payment',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            )
                        )
                    ),
                    'all-classes' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/all-classes',
                            'defaults' => array(
                                'controller' => 'EducationalCenter\Controller\Workshop',
                                'action' => 'classes',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array()
                    ),
                    'workshop' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/workshop[/:workshop]',
                            'defaults' => array(
                                'controller' => 'EducationalCenter\Controller\Workshop',
                                'action' => 'workshop',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'classes' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/classes',
                                    'defaults' => array(
                                        'controller' => 'EducationalCenter\Controller\Workshop',
                                        'action' => 'classes',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'class' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/class[/:class]',
                                    'defaults' => array(
                                        'controller' => 'EducationalCenter\Controller\Workshop',
                                        'action' => 'class',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'agreement' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/agreement',
                                            'defaults' => array(
                                                'controller' => 'EducationalCenter\Controller\Workshop',
                                                'action' => 'agreement',
                                            ),
                                        ),
                                    ),
                                    'register' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/register',
                                            'defaults' => array(
                                                'controller' => 'EducationalCenter\Controller\Workshop',
                                                'action' => 'register',
                                            ),
                                        ),
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            'admin' => array(
                'child_routes' => array(
                    'educational-center' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/educational-center',
                            'defaults' => array(
                                'controller' => 'EducationalCenter\Controller\EducationalCenter',
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
                                        'controller' => 'EducationalCenter\Controller\EducationalCenter',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                            'my-registered-workshop-classes' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/my-registered-workshop-classes',
                                    'defaults' => array(
                                        'controller' => 'EducationalCenter\Controller\WorkshopAttendance',
                                        'action' => 'my-workshop-classes',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'cancel-request' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/cancel-request',
                                            'defaults' => array(
                                                'controller' => 'EducationalCenter\Controller\WorkshopAttendance',
                                                'action' => 'cancel-request',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'my-workshop-classes' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/my-workshop-classes',
                                    'defaults' => array(
                                        'controller' => 'EducationalCenter\Controller\WorkshopClass',
                                        'action' => 'my-workshop-classes',
                                    ),
                                ),
                            ),
                            'participant-panel' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/participant-panel[/:id]',
                                    'defaults' => array(
                                        'controller' => 'EducationalCenter\Controller\WorkshopAttendance',
                                        'action' => 'participant-panel',
                                    ),
                                ),
                            ),
                            'edit-signup-form' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/edit-signup-form[/:workshop/:class]',
                                    'defaults' => array(
                                        'controller' => 'EducationalCenter\Controller\WorkshopAttendance',
                                        'action' => 'edit-signup-form',
                                    ),
                                ),
                            ),
                            'attendance' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/attendance',
                                    'defaults' => array(
                                        'controller' => 'EducationalCenter\Controller\WorkshopAttendance',
                                        'action' => 'attendance',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'cancel' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/cancel',
                                            'defaults' => array(
                                                'controller' => 'EducationalCenter\Controller\WorkshopAttendance',
                                                'action' => 'cancel',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'workshop' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/workshop',
                                    'defaults' => array(
                                        'controller' => 'EducationalCenter\Controller\WorkshopAdmin',
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
                                                'controller' => 'EducationalCenter\Controller\WorkshopAdmin',
                                                'action' => 'new',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'EducationalCenter\Controller\WorkshopAdmin',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                    'change-status' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/change-status/:id/:status',
                                            'defaults' => array(
                                                'controller' => 'EducationalCenter\Controller\WorkshopAdmin',
                                                'action' => 'change-status',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'EducationalCenter\Controller\WorkshopAdmin',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'class' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:workshop/class',
                                            'defaults' => array(
                                                'controller' => 'EducationalCenter\Controller\WorkshopClass',
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
                                                        'controller' => 'EducationalCenter\Controller\WorkshopClass',
                                                        'action' => 'new',
                                                    ),
                                                ),
                                            ),
                                            'edit' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/:id/edit',
                                                    'defaults' => array(
                                                        'controller' => 'EducationalCenter\Controller\WorkshopClass',
                                                        'action' => 'edit',
                                                    ),
                                                ),
                                            ),
                                            'delete' => array(
                                                'type' => 'Literal',
                                                'options' => array(
                                                    'route' => '/delete',
                                                    'defaults' => array(
                                                        'controller' => 'EducationalCenter\Controller\WorkshopClass',
                                                        'action' => 'delete',
                                                    ),
                                                ),
                                            ),
                                            'change-status' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/change-status/:id/:status',
                                                    'defaults' => array(
                                                        'controller' => 'EducationalCenter\Controller\WorkshopClass',
                                                        'action' => 'change-status',
                                                    ),
                                                ),
                                            ),
                                            'cancel' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/:id/cancel',
                                                    'defaults' => array(
                                                        'controller' => 'EducationalCenter\Controller\WorkshopClass',
                                                        'action' => 'cancel',
                                                    ),
                                                ),
                                            ),
                                            'timetable' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/:class/timetable',
                                                    'defaults' => array(
                                                        'controller' => 'EducationalCenter\Controller\WorkshopTime',
                                                        'action' => 'index',
                                                    ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array(
                                                    'new' => array(
                                                        'type' => 'Segment',
                                                        'options' => array(
                                                            'route' => '/new[/:type]',
                                                            'defaults' => array(
                                                                'controller' => 'EducationalCenter\Controller\WorkshopTime',
                                                                'action' => 'new',
                                                            ),
                                                        ),
                                                    ),
                                                    'delete' => array(
                                                        'type' => 'Literal',
                                                        'options' => array(
                                                            'route' => '/delete',
                                                            'defaults' => array(
                                                                'controller' => 'EducationalCenter\Controller\WorkshopTime',
                                                                'action' => 'delete',
                                                            ),
                                                        ),
                                                    ),
                                                    'change-status' => array(
                                                        'type' => 'Segment',
                                                        'options' => array(
                                                            'route' => '/change-status/:id/:status',
                                                            'defaults' => array(
                                                                'controller' => 'EducationalCenter\Controller\WorkshopTime',
                                                                'action' => 'change-status',
                                                            ),
                                                        ),
                                                    ),
                                                )
                                            ),
                                            'attendance' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/:class/attendance',
                                                    'defaults' => array(
                                                        'controller' => 'EducationalCenter\Controller\WorkshopAttendance',
                                                        'action' => 'index',
                                                    ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array(
                                                    'cancel' => array(
                                                        'type' => 'Segment',
                                                        'options' => array(
                                                            'route' => '/:id/cancel',
                                                            'defaults' => array(
                                                                'controller' => 'EducationalCenter\Controller\WorkshopAttendance',
                                                                'action' => 'cancel',
                                                            ),
                                                        ),
                                                    ),
                                                )
                                            ),
                                        )
                                    ),
                                )
                            ),
                        )
                    ),
                    'reports' => array(
                        'child_routes' => array()
                    )
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Educational Center',
                        'route' => 'admin/educational-center/config',
                        'resource' => 'route:admin/educational-center/config',
                    ),
                )
            ),
            'educational-center' => array(
                'label' => 'Educational Center',
                'route' => 'admin/educational-center',
                'resource' => 'route:admin/educational-center',
                'pages' => array(
                    array(
                        'label' => 'EDUCATIONAL_CENTER_WORKSHOP',
                        'route' => 'admin/educational-center/workshop',
                        'resource' => 'route:admin/educational-center/workshop',
                        'pages' => array(
                            array(
                                'label' => 'New',
                                'route' => 'admin/educational-center/workshop/new',
                                'resource' => 'route:admin/educational-center/workshop/new',
                            ),
                            array(
                                'label' => 'Edit',
                                'route' => 'admin/educational-center/workshop/edit',
                                'resource' => 'route:admin/educational-center/workshop/edit',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Classes',
                                'route' => 'admin/educational-center/workshop/class',
                                'resource' => 'route:admin/educational-center/workshop/class',
                                'visible' => false,
                                'pages' => array(
                                    array(
                                        'label' => 'New',
                                        'route' => 'admin/educational-center/workshop/class/new',
                                        'resource' => 'route:admin/educational-center/workshop/class/new',
                                        'visible' => false
                                    ),
                                    array(
                                        'label' => 'Edit',
                                        'route' => 'admin/educational-center/workshop/class/edit',
                                        'resource' => 'route:admin/educational-center/workshop/class/edit',
                                        'visible' => false
                                    ),
                                    array(
                                        'label' => 'Time Table',
                                        'route' => 'admin/educational-center/workshop/class/timetable',
                                        'resource' => 'route:admin/educational-center/workshop/class/timetable',
                                        'visible' => false,
                                        'pages' => array(
                                            array(
                                                'label' => 'New',
                                                'route' => 'admin/educational-center/workshop/class/timetable/new',
                                                'resource' => 'route:admin/educational-center/workshop/class/timetable/new',
                                                'visible' => false
                                            ),
                                        )
                                    ),
                                    array(
                                        'label' => 'Attendances',
                                        'route' => 'admin/educational-center/workshop/class/attendance',
                                        'resource' => 'route:admin/educational-center/workshop/class/attendance',
                                        'visible' => false,
                                        'pages' => array(
                                            array(
                                                'label' => 'Cancel',
                                                'route' => 'admin/educational-center/workshop/class/attendance/cancel',
                                                'resource' => 'route:admin/educational-center/workshop/class/attendance/cancel',
                                                'visible' => false
                                            ),
                                        )
                                    ),
                                )
                            ),
                        )
                    ),
                    array(
                        'label' => 'Config',
                        'route' => 'admin/educational-center/config',
                        'resource' => 'route:admin/educational-center/config',
                    ),
                    array(
                        'label' => 'My Panel',
                        'route' => 'admin/educational-center/participant-panel',
                        'resource' => 'route:admin/educational-center/participant-panel:menu',
                        'pages' => array(
                            array(
                                'label' => 'Signup Form',
                                'route' => 'admin/educational-center/edit-signup-form',
                                'resource' => 'route:admin/educational-center/edit-signup-form',
                                'visible' => false
                            ),
                        )
                    ),
                    array(
                        'label' => 'My Workshop Classes',
                        'route' => 'admin/educational-center/my-registered-workshop-classes',
                        'resource' => 'route:admin/educational-center/my-registered-workshop-classes',
                    ),
//                    array(
//                        'label' => 'My Workshop Classes',
//                        'route' => 'admin/educational-center/my-workshop-classes',
//                        'resource' => 'route:admin/educational-center/my-workshop-classes',
//                    ),
                    array(
                        'label' => 'Attendances',
                        'route' => 'admin/educational-center/attendance',
                        'resource' => 'route:admin/educational-center/attendance',
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
        'invokables' => array(),
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
                'EducationalCenter' => __DIR__ . '/../public',
            ),
        ),
    ),
    'template_placeholders' => array(
        'Workshop' => array(
            '__WORKSHOP_CLASS_REQUEST_ID__' => 'workshop class registration id',
            '__WORKSHOP_CLASS_NAME__' => 'title of the workshop class',
            '__WORKSHOP_CLASS_URL__' => 'url of the workshop class',
            '__WORKSHOP_TIME_FOR_PAYMENT__' => 'maximum time limit for workshop class registration payment',
            '__WORKSHOP_CLASS_CANCEL_REASON__' => 'the reason why the user requested to cancel its class or why admin canceled the class',
        )
    ),
    'fields_entities' => array(
        'workshop_signup_form' => 'Workshop Signup Form'
    )
);