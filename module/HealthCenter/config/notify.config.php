<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/12/14
 * Time: 1:36 PM
 */
return array(
    'HealthCenter' => array(
        'reservation_initialized' => array(
            'label' => 'reservation (initialized)',
            'description' => 'after a user has reserved a appointment but has not done the payment yet',
            'notify_with' => array(
                'email' => 'health-center/notify/reservation-initialized',
            ),
        ),
        'reservation_finalized' => array(
            'label' => 'Reservation Finalized',
            'description' => 'User has finalized doctor time reservation',
            'notify_with' => array(
                'sms' => 'Appointment Reserved for __DOCTOR_NAME__ at __DOCTOR_RESERVE_TIME__',
                'email' => 'health-center/notify/reservation-finalized',
                'internal' => 'Appointment Reserved for __DOCTOR_URL__ at __DOCTOR_RESERVE_TIME__'
            ),
        ),
        'reservation_transferred' => array(
            'label' => 'Reservation Transferred',
            'description' => 'User has transferred doctor time reservation',
            'notify_with' => array(
                'sms' => 'Appointment transferred for __DOCTOR_NAME__ from __DOCTOR_TRANSFER_TIME__ to __DOCTOR_RESERVE_TIME__',
                'email' => 'health-center/notify/reservation-transferred',
                'internal' => 'Appointment transferred for __DOCTOR_URL__ from __DOCTOR_TRANSFER_TIME__ to __DOCTOR_RESERVE_TIME__'
            ),
        ),
        'reservation_transferred_to_doctor' => array(
            'label' => 'Reservation Transferred',
            'description' => 'notify the doctor about user transferring reservation',
            'notify_with' => array(
                'sms' => 'Appointment transferred for __USER_NAME__ from __DOCTOR_TRANSFER_TIME__ to __DOCTOR_RESERVE_TIME__',
                'email' => 'health-center/notify/reservation-transferred-to-doctor',
                'internal' => 'Appointment transferred for __USER_URL__ from __DOCTOR_TRANSFER_TIME__ to __DOCTOR_RESERVE_TIME__'
            ),
        ),
        'reserve_cancel_request' => array(
            'label' => 'reservation cancel request',
            'description' => "notify the user that their cancel request has been send to admin",
            'notify_with' => array(
                'email' => 'health-center/notify/reserve-cancel-request',
                'internal' => 'your request to cancel your reservation for __DOCTOR_URL__ has been send for the administration',
            ),
        ),
        'reserve_cancel_request_admin' => array(
            'label' => 'reserve cancel request (admin)',
            'description' => "notify the admin that a user has requested to cancel their reservation",
            'notify_with' => array(
                'email' => 'health-center/notify/reserve-cancel-request-admin',
                'internal' => '__USER_URL__ has requested to cancel their registration(__RESERVATION__) for __DOCTOR_URL__',
            ),
        ),
        'reserve_cancel_request_response' => array(
            'label' => 'reservation canceled',
            'description' => "notify the user that their cancel request has been processed",
            'notify_with' => array(
                'sms' => 'your request to cancel __DOCTOR__ has been processed by administration',
                'email' => 'health-center/notify/reserve-cancel-request-processed',
                'internal' => 'your request to cancel __DOCTOR__ has been processed by administration',
            ),
        ),
        'reserve_cancel_doctor' => array(
            'label' => 'Reserve Canceled(Doctor)',
            'description' => "notifying the doctor about a reservation getting canceled",
            'notify_with' => array(
                'sms' => 'the session at __RESERVE_DATE__ is canceled',
                'email' => 'health-center/notify/reserve-canceled-to-doctor',
                'internal' => 'the session at __RESERVE_DATE__ is canceled',
            ),
        ),
        'reserve_cancel_user' => array(
            'label' => 'Reserve Canceled(User)',
            'description' => "notifying the user about a reservation getting canceled",
            'notify_with' => array(
                'sms' => 'the session at __RESERVE_DATE__ for __DOCTOR__ is canceled',
                'email' => 'health-center/notify/reserve-canceled-to-user',
                'internal' => 'the session at __RESERVE_DATE__ for __DOCTOR__ is canceled',
            ),
        ),
        'reserve_cancel_admin' => array(
            'label' => 'Reserve Canceled(Admin)',
            'description' => "notify the admin that a doctor has canceled their reservation",
            'notify_with' => array(
                'sms' => "__DOCTOR__ has canceled __USER__'s reservation(__RESERVATION__)",
                'email' => 'health-center/notify/reserve-canceled-to-admin',
                'internal' => "__DOCTOR__ has canceled __USER__'s reservation(__RESERVATION__)",
            ),
        ),
        'refer' => array(
            'label' => 'Refer',
            'description' => "notifying the doctor that a patient has been referred to him/her",
            'notify_with' => array(
                'sms' => '__DOCTOR__ has referred __USER__ to you',
                'email' => 'health-center/notify/refer',
                'internal' => '__DOCTOR__ has referred __USER__ to you',
            ),
        ),
    )
);