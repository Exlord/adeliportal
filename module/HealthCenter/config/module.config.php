<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace HealthCenter;
return array(
    'service_manager' => array(
        'invokables' => array(
            'hc_doctor_time_table' => 'HealthCenter\Model\DoctorTimeTable',
            'hc_doctor_table' => 'HealthCenter\Model\DoctorTable',
            'hc_doctor_profile_table' => 'HealthCenter\Model\DoctorProfileTable',
            'hc_doctor_reservation' => 'HealthCenter\Model\DoctorReservationTable',
            'hc_doctor_ref_table' => 'HealthCenter\Model\DoctorRefTable',
            'hc_api' => 'HealthCenter\API\HC',
            'hc_event_manager' => 'HealthCenter\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'HealthCenter\Controller\HealthCenter' => 'HealthCenter\Controller\HealthCenter',
            'HealthCenter\Controller\Doctor' => 'HealthCenter\Controller\Doctor',
            'HealthCenter\Controller\DoctorTime' => 'HealthCenter\Controller\DoctorTime',
            'HealthCenter\Controller\Patient' => 'HealthCenter\Controller\Patient',
            'HealthCenter\Controller\Reservations' => 'HealthCenter\Controller\Reservations',
        ),
    ),
    'router' => array(
        'routes' => array(
            'app' => array(
                'child_routes' => array(
                    'health-center' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/health-center',
                            'defaults' => array(
                                'controller' => 'HealthCenter\Controller\HealthCenter',
                                'action' => 'health-center',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'search-by' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/search-by',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\HealthCenter',
                                        'action' => 'search-by',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'specialization' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/specialization',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\HealthCenter',
                                        'action' => 'specialization',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'doctors' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/doctors[/:spec[/:spec-title]]',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\HealthCenter',
                                        'action' => 'doctors',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'doctor' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/doctor[/:id[/:day]]',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\HealthCenter',
                                        'action' => 'doctor',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'time-line' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/time-line/:id/:day',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\HealthCenter',
                                        'action' => 'time-line',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'agreement' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/agreement/:doctor/:time',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\HealthCenter',
                                        'action' => 'agreement',
                                    ),
                                ),
                            ),
                            'reserve' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/reserve/:doctor/:time',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\HealthCenter',
                                        'action' => 'reserve',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'patient-edit-profile' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/patient-profile/:doctor/:time',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\Patient',
                                        'action' => 'edit-profile',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            ),
                            'finalize-payment' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/finalize-payment/:params/:paymentId',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\HealthCenter',
                                        'action' => 'finalize-payment',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array()
                            )
                        )
                    ),

                )
            ),
            'admin' => array(
                'child_routes' => array(
                    'health-center' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/health-center',
                            'defaults' => array(
                                'controller' => 'HealthCenter\Controller\HealthCenter',
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
                                        'controller' => 'HealthCenter\Controller\HealthCenter',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                            'reservations' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/reservations',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\Reservations',
                                        'action' => 'index',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'cancel' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:resId/cancel',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Reservations',
                                                'action' => 'cancel',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array()
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Reservations',
                                                'action' => 'delete',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array()
                                    ),
                                )
                            ),
                            'patient-panel' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/patient',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\Patient',
                                        'action' => 'panel',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'edit-profile' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit-profile[/:id]',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Patient',
                                                'action' => 'edit-profile',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array()
                                    ),
                                    'my-reservations' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/my-reservations',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Reservations',
                                                'action' => 'index',
                                                'visitor' => 'patient'
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array(
                                            'cancel-request' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/:resId/cancel-request',
                                                    'defaults' => array(
                                                        'controller' => 'HealthCenter\Controller\Reservations',
                                                        'action' => 'cancel-request',
                                                    ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array()
                                            ),
                                        )
                                    ),
                                )
                            ),
                            'doctor-panel' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/doctor',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\Doctor',
                                        'action' => 'panel',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'my-reservations' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/my-reservations',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Reservations',
                                                'action' => 'index',
                                                'visitor' => 'doctor'
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array(
                                            'cancel' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/:resId/cancel',
                                                    'defaults' => array(
                                                        'controller' => 'HealthCenter\Controller\Reservations',
                                                        'action' => 'cancel',
                                                    ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array()
                                            ),
                                        )
                                    ),
                                    'visit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/visit/:patient/:resId',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Doctor',
                                                'action' => 'visit',
                                            ),
                                        ),
                                    ),
                                    'refer' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/refer/:patient[/:doctor]',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Doctor',
                                                'action' => 'refer',
                                            ),
                                        ),
                                    ),
                                    'patient' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/patient/:patient',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Patient',
                                                'action' => 'profile',
                                            ),
                                        ),
                                    ),
                                    'my-patients' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/my-patients',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Doctor',
                                                'action' => 'patients',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'doctors' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/doctors',
                                    'defaults' => array(
                                        'controller' => 'HealthCenter\Controller\Doctor',
                                        'action' => 'doctors',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'edit-profile' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit-profile',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Doctor',
                                                'action' => 'edit-profile',
                                            ),
                                        ),
                                    ),
                                    'reservations' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:doctorId/reservations',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Reservations',
                                                'action' => 'index',
                                            ),
                                        ),
                                    ),
                                    'patients' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/patients',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\Doctor',
                                                'action' => 'patients',
                                            ),
                                        ),
                                    ),
                                    'timetable' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:doctor/timetable',
                                            'defaults' => array(
                                                'controller' => 'HealthCenter\Controller\DoctorTime',
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
                                                        'controller' => 'HealthCenter\Controller\DoctorTime',
                                                        'action' => 'new',
                                                    ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array()
                                            ),
                                            'change-status' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/change-status/:id/:status',
                                                    'defaults' => array(
                                                        'controller' => 'HealthCenter\Controller\DoctorTime',
                                                        'action' => 'change-status',
                                                    ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array()
                                            ),
                                            'delete' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/:id/delete',
                                                    'defaults' => array(
                                                        'controller' => 'HealthCenter\Controller\DoctorTime',
                                                        'action' => 'delete',
                                                    ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array()
                                            ),
                                        )
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
            'health-center' => array(
                'label' => 'Health/Counseling Center',
                'route' => 'admin/health-center',
                'resource' => 'route:admin/health-center',
                'pages' => array(
                    array(
                        'label' => 'Configs',
                        'route' => 'admin/health-center/config',
                        'resource' => 'route:admin/health-center/config',
                    ),
                    array(
                        'label' => 'My Panel',
                        'route' => 'admin/health-center/doctor-panel',
                        'resource' => 'route:admin/health-center/doctor-panel',
                        'pages' => array(
                            array(
                                'label' => 'Consulting/Health Records',
                                'route' => 'admin/health-center/doctor-panel/patient',
                                'resource' => 'route:admin/health-center/doctor-panel/patient',
                                'visible' => false,
                                'pages' => array()
                            ),
                            array(
                                'label' => 'My Reservations',
                                'route' => 'admin/health-center/doctor-panel/my-reservations',
                                'resource' => 'route:admin/health-center/doctor-panel/my-reservations',
                                'pages' => array(
                                    array(
                                        'label' => 'Cancel',
                                        'route' => 'admin/health-center/doctor-panel/my-reservations/cancel',
                                        'resource' => 'route:admin/health-center/doctor-panel/my-reservations/cancel',
                                        'visible' => false,
                                        'pages' => array()
                                    ),
                                )
                            ),
                            array(
                                'label' => 'My Patients',
                                'route' => 'admin/health-center/doctor-panel/my-patients',
                                'resource' => 'route:admin/health-center/doctor-panel/my-patients',
                                'pages' => array()
                            ),
                            array(
                                'label' => 'HC_DOCTOR_REFER',
                                'route' => 'admin/health-center/doctor-panel/refer',
                                'resource' => 'route:admin/health-center/doctor-panel/refer',
                                'visible' => false
                            ),
                        )
                    ),
                    array(
                        'label' => 'My Panel',
                        'route' => 'admin/health-center/patient-panel',
                        'resource' => 'route:admin/health-center/patient-panel',
                        'pages' => array(
                            array(
                                'label' => 'Edit Profile',
                                'route' => 'admin/health-center/patient-panel/edit-profile',
                                'resource' => 'route:admin/health-center/patient-panel/edit-profile',
                                'pages' => array()
                            ),
                            array(
                                'label' => 'My Reservations',
                                'route' => 'admin/health-center/patient-panel/my-reservations',
                                'resource' => 'route:admin/health-center/patient-panel/my-reservations',
                                'pages' => array(
                                    array(
                                        'label' => 'Cancel Request',
                                        'route' => 'admin/health-center/patient-panel/my-reservations/cancel-request',
                                        'resource' => 'route:admin/health-center/patient-panel/my-reservations/cancel-request',
                                        'visible' => false,
                                        'pages' => array()
                                    ),
                                )
                            ),
                        )
                    ),
                    array(
                        'label' => 'Doctors/Counselors',
                        'route' => 'admin/health-center/doctors',
                        'resource' => 'route:admin/health-center/doctors',
                        'pages' => array(
                            array(
                                'label' => 'Edit Profile',
                                'route' => 'admin/health-center/doctors/edit-profile',
                                'resource' => 'route:admin/health-center/doctors/edit-profile',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Reservations',
                                'route' => 'admin/health-center/doctors/reservations',
                                'resource' => 'route:admin/health-center/doctors/reservations',
                                'visible' => false,
                            ),
                            array(
                                'label' => 'Patients',
                                'route' => 'admin/health-center/doctors/patients',
                                'resource' => 'route:admin/health-center/doctors/patients',
                                'visible' => false
                            ),
                            array(
                                'label' => 'Time Table',
                                'route' => 'admin/health-center/doctors/timetable',
                                'resource' => 'route:admin/health-center/doctors/timetable',
                                'visible' => false,
                                'pages' => array(
                                    array(
                                        'label' => 'New',
                                        'route' => 'admin/health-center/doctors/timetable/new',
                                        'resource' => 'route:admin/health-center/doctors/timetable/new',
                                        'visible' => false,
                                        'pages' => array()
                                    ),
                                )
                            ),
                        )
                    ),
                    array(
                        'label' => 'Reservations',
                        'route' => 'admin/health-center/reservations',
                        'resource' => 'route:admin/health-center/reservations',
                        'pages' => array(
                            array(
                                'label' => 'Cancel',
                                'route' => 'admin/health-center/reservations/cancel',
                                'resource' => 'route:admin/health-center/reservations/cancel',
                                'visible' => false,
                                'pages' => array()
                            ),
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
                'HealthCenter' => __DIR__ . '/../public',
            ),
        ),
    ),
    'fields_entities' => array(
        'medical_records' => 'Medical Records'
    )
);