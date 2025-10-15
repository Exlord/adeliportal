<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 9:44 AM
 */

namespace HealthCenter\API;


use HealthCenter\Module;

class ACL {
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Health/Counseling Center',
                    'route' => 'route:admin/health-center',
                    'child_route' => array(
                        array(
                            'label' => 'Configs',
                            'route' => 'route:admin/health-center/config',
                            'child_route' => array(),
                        ),
                        array(
                            'label' => 'Reservations',
                            'route' => 'route:admin/health-center/reservations',
                            'child_route' => array(
                                array(
                                    'label' => 'Cancel',
                                    'route' => 'route:admin/health-center/reservations/cancel',
                                    'child_route' => array(
                                        array(
                                            'label' => 'Cancel All',
                                            'note' => 'cancel every doctor/patients reservations',
                                            'route' => 'route:admin/health-center/reservations/cancel:all',
                                            'child_route' => array(),
                                        ),
                                    ),
                                ),
                                array(
                                    'label' => 'Delete All',
                                    'note' => 'delete every doctor/patients reservations',
                                    'route' => Module::RESERVATION_DELETE,
                                    'child_route' => array(),
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Doctor Panel',
                            'route' => 'route:admin/health-center/doctor-panel',
                            'child_route' => array(
                                array(
                                    'label' => 'Visit Patient',
                                    'route' => 'route:admin/health-center/doctor-panel/visit',
                                    'child_route' => array(),
                                ),
                                array(
                                    'label' => 'Refer',
                                    'note' => 'refer the patient to another doctor',
                                    'route' => 'route:admin/health-center/doctor-panel/refer',
                                    'child_route' => array(),
                                ),
                                array(
                                    'label' => 'Consulting/Health Records',
                                    'route' => 'route:admin/health-center/doctor-panel/patient',
                                    'child_route' => array(),
                                ),
                                array(
                                    'label' => 'My Patients',
                                    'route' => 'route:admin/health-center/doctor-panel/my-patients',
                                    'child_route' => array(),
                                ),
                                array(
                                    'label' => 'My Reservations',
                                    'route' => 'route:admin/health-center/doctor-panel/my-reservations',
                                    'child_route' => array(),
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Patient Panel',
                            'route' => 'route:admin/health-center/patient-panel',
                            'child_route' => array(
                                array(
                                    'label' => 'Edit Profile',
                                    'route' => 'route:admin/health-center/patient-panel/edit-profile',
                                    'child_route' => array(),
                                ),
                                array(
                                    'label' => 'Patient Reservations',
                                    'route' => 'route:admin/health-center/patient-panel/my-reservations',
                                    'child_route' => array(
                                        array(
                                            'label' => 'Cancel Request',
                                            'route' => 'route:admin/health-center/patient-panel/my-reservations/cancel-request',
                                            'child_route' => array(),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Doctors/Counselors',
                            'route' => 'route:admin/health-center/doctors',
                            'child_route' => array(
                                array(
                                    'label' => 'Edit Profile',
                                    'route' => 'route:admin/health-center/doctors/profile',
                                    'child_route' => array(),
                                ),
                                array(
                                    'label' => 'Reservations',
                                    'route' => 'route:admin/health-center/doctors/reservations',
                                    'child_route' => array(),
                                ),
                                array(
                                    'label' => 'Patients',
                                    'route' => 'route:admin/health-center/doctors/patients',
                                    'child_route' => array(),
                                ),
                                array(
                                    'label' => 'Time Table',
                                    'route' => 'route:admin/health-center/doctors/timetable',
                                    'child_route' => array(
                                        array(
                                            'label' => 'New',
                                            'route' => 'route:admin/health-center/doctors/timetable/new',
                                            'child_route' => array(),
                                        ),
                                        array(
                                            'label' => 'Change Status',
                                            'route' => 'route:admin/health-center/doctors/timetable/change-status',
                                            'child_route' => array(
                                                array(
                                                    'label' => 'Change Status (All)',
                                                    'note' => 'change all timetable entries status belonging to any doctor',
                                                    'route' => Module::TIMETABLE_CHANGE_ALL,
                                                    'child_route' => array(),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );


            return $dataItemAcl;
    }
} 