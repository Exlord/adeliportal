<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:52 AM
 */

namespace EducationalCenter\API;


use EducationalCenter\Module;

class ACL {
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Educational Center',
                    'route' => 'route:admin/educational-center',
                    'child_route' => array(
                        array(
                            'label' => 'Workshop',
                            'route' => 'route:admin/educational-center/workshop',
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'route' => 'route:admin/educational-center/workshop/new',
                                ),
                                array(
                                    'label' => 'Edit',
                                    'route' => 'route:admin/educational-center/workshop/edit',
                                ),
                                array(
                                    'label' => 'Delete',
                                    'route' => 'route:admin/educational-center/workshop/delete',
                                ),
                                array(
                                    'label' => 'Change Status',
                                    'route' => 'route:admin/educational-center/workshop/change-status',
                                ),
                                array(
                                    'label' => 'Classes',
                                    'note' => 'create ,edit and delete classes for the selected workshop',
                                    'route' => 'route:admin/educational-center/workshop/class',
                                    'child_route' => array(
                                        array(
                                            'label' => 'Create',
                                            'route' => 'route:admin/educational-center/workshop/class/new',
                                        ),
                                        array(
                                            'label' => 'Edit',
                                            'route' => 'route:admin/educational-center/workshop/class/edit',
                                        ),
                                        array(
                                            'label' => 'Change Status',
                                            'route' => 'route:admin/educational-center/workshop/class/change-status',
                                        ),
                                        array(
                                            'label' => 'Delete',
                                            'route' => 'route:admin/educational-center/workshop/class/delete',
                                        ),
                                        array(
                                            'label' => 'Time Table',
                                            'note' => 'change time table for the selected workshop',
                                            'route' => Module::TIMETABLE,
                                            'child_route' => array(
                                                array(
                                                    'label' => 'Create',
                                                    'route' => 'route:admin/educational-center/workshop/class/timetable/new',
                                                ),
                                                array(
                                                    'label' => 'Change Status',
                                                    'route' => Module::TIMETABLE_CHANGE_STATUS,
                                                    'child_route' => array(
                                                        array(
                                                            'label' => 'Change Status (All)',
                                                            'note' => 'change all time table entry status (allow only for admin roles)',
                                                            'route' => Module::TIMETABLE_CHANGE_STATUS_ALL,
                                                        )
                                                    )
                                                ),
                                                array(
                                                    'label' => 'Delete',
                                                    'route' => 'route:admin/educational-center/workshop/class/timetable/delete',
                                                ),
                                            )
                                        ),
                                        array(
                                            'label' => 'Attendance',
                                            'note' => 'view attendances list for the selected class',
                                            'route' => Module::ATTENDANCE,
                                            'child_route' => array(
                                                array(
                                                    'label' => 'Cancel',
                                                    'note' => 'cancel a users class registration',
                                                    'route' => Module::ATTENDANCE_CANCEL,
                                                )
                                            )
                                        ),
                                    )
                                ),

                            ),
                        ),
                        array(
                            'label' => 'Config',
                            'route' => 'route:admin/educational-center/config',
                        ),
                        array(
                            'label' => 'Participant Panel',
                            'note' => 'My Panel Menu Item',
                            'route' => 'route:admin/educational-center/participant-panel:menu',
                        ),
                        array(
                            'label' => "Participants Profile",
                            'note' => '',
                            'route' => 'route:admin/educational-center/participant-panel',
                            'child_route' => array()
                        ),
                        array(
                            'label' => 'My Registered Workshop Classes',
                            'note' => 'the classes that the current user has registered in',
                            'route' => 'route:admin/educational-center/my-registered-workshop-classes',
                            'child_route' => array(
                                array(
                                    'label' => 'Cancel My Workshop Class',
                                    'route' => 'route:admin/educational-center/my-registered-workshop-classes/cancel',
                                ),
                            )
                        ),
                        array(
                            'label' => 'My Workshop Classes',
                            'note' => 'the classes that the current user is teaching in',
                            'route' => 'route:admin/educational-center/my-workshop-classes',
                        ),
                        array(
                            'label' => 'Attendances',
                            'route' => 'route:admin/educational-center/attendance',
                        ),
                    ),
                ),
            ),
        );

            return $dataItemAcl;
    }

} 