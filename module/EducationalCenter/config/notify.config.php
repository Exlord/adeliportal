<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/12/14
 * Time: 1:36 PM
 */
return array(
    'EducationalCenter' => array(
        'workshop_user_reg_temp' => array(
            'label' => 'register (initialized)',
            'description' => 'after a user has registered for a workshop class but has not done the payment yet',
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-user-reg-temp',
            ),
        ),
        'workshop_user_reg_fin' => array(
            'label' => 'register (finalized)',
            'description' => 'after a user has finalized workshop registration with successful payment',
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-user-reg-fin',
                'sms' => 'you have finalized your registration for the __WORKSHOP_CLASS_NAME__'
            ),
        ),
        'workshop_new_class_educator' => array(
            'label' => 'New Class',
            'description' => 'notifying the educator about workshop class changes',
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-new-class',
                'internal' => 'You have been assigned to __WORKSHOP_CLASS_URL__'
            ),
        ),
        'workshop_class_modified' => array(
            'label' => 'Class Modified',
            'description' => 'notifying the educator/users about workshop class changes',
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-class-modified',
                'internal' => '__WORKSHOP_CLASS_URL__ has been modified',
                'sms' => '__WORKSHOP_CLASS_NAME__ has been modified',
            ),
        ),
        'workshop_class_educator_changed' => array(
            'label' => 'Class Educator changed',
            'description' => "notifying the educator's about workshop class changes",
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-class-educator-changed',
                'internal' => 'The educator for __WORKSHOP_CLASS_URL__ is changed from __WORKSHOP_CLASS_EDUCATOR__ to __WORKSHOP_CLASS_NEW_EDUCATOR__.',
            ),
        ),
        'workshop_class_timetable_changed' => array(
            'label' => 'Class Timetable changed',
            'description' => "notifying the educator and users about workshop class timetable changes",
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-class-timetable-changed',
                'internal' => 'The timetable for __WORKSHOP_CLASS_URL__ is changed.',
            ),
        ),
        'workshop_class_canceled' => array(
            'label' => 'Class Canceled',
            'description' => "notifying the educator and users about workshop class getting canceled",
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-class-canceled',
                'sms' => 'The __WORKSHOP_CLASS_NAME__ is canceled.',
                'internal' => 'The __WORKSHOP_CLASS_URL__ is canceled.',
            ),
        ),
        'workshop_class_cancel_request' => array(
            'label' => 'class cancel request',
            'description' => "notify the user that their cancel request has been send to admin",
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-class-cancel-request',
                'internal' => 'your request to cancel __WORKSHOP_CLASS_URL__ has been send for the administration',
            ),
        ),
        'workshop_class_cancel_request_admin' => array(
            'label' => 'class cancel request (admin)',
            'description' => "notify the admin that a user has requested to cancel their class",
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-class-cancel-request-admin',
                'internal' => '__USER_URL__ has requested to cancel their registration(__WORKSHOP_ATTENDANCE__) for __WORKSHOP_CLASS_URL__',
            ),
        ),
        'workshop_class_reg_canceled' => array(
            'label' => 'class registration canceled',
            'description' => "notify the user that their cancel request has been processed",
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-class-reg-canceled',
                'sms' => 'your request to cancel __WORKSHOP_CLASS_NAME__ has been processed by administration',
                'internal' => 'your request to cancel __WORKSHOP_CLASS_URL__ has been processed by administration',
            ),
        ),
        'workshop_class_before_start' => array(
            'label' => 'Before class start',
            'description' => "notify the user that their class will be started soon",
            'notify_with' => array(
                'email' => 'educational-center/notify/workshop-class-before-start',
                'sms' => 'your class __WORKSHOP_CLASS_NAME__ will start in __WORKSHOP_CLASS_START_TIME__',
                'internal' => 'your class __WORKSHOP_CLASS_URL__ will start in __WORKSHOP_CLASS_START_TIME__',
            ),
        ),
    )
);