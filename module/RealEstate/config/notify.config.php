<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/20/14
 * Time: 8:58 AM
 */
return array(
    'RealEstate' => array(
        'estate_registration_in_agent_area' => array(//zamani ke melk jadid sabt shod baraye moshvere amlake sahebe manghate notify bere
            'label' => 'realEstate_estate_registration_in_your_area',
            'description' => 'realEstate_send_notify_to_Agent_area',
            'notify_with' => array(
               // 'sms' => 'realEstate_new_notify_sms_estate_registration_agent_area_with_CODE',
                'email' => 'real-estate/notify/estate_registration_in_agent_area'
            ),
        ),
        'new_estate_registration' => array(//zamani ke melk jadid sabt shod baraye sahebe melk notify bere
            'label' => 'realEstate_New_Estate_registration',
            'description' => 'realEstate_send_notify_to_homeowners',
            'notify_with' => array(
                'sms' => t('new_realEstate_notify_sms_registration_with_CODE'),
                'email' => 'real-estate/notify/new-email-template'
            ),
        ),
        'new_estate_registrationForManage' => array(//zamani ke melk jadid sabt shod baraye modir notify bere
            'label' => 'realEstate_New_Estate_registrationForManage',
            'description' => 'realEstate_send_notify_to_manageDesc',
            'notify_with' => array(
               // 'sms' => t('new_realEstate_notify_sms_registration_with_CODE'),
                'email' => 'real-estate/notify/new-email-template-to-manage'
            ),
        ),
        'approved_estate' => array(//zamani ke melk jadid tavassot modir taiid mishavad
            'label' => 'realEstate_approved_estate',
            'description' => 'realEstate_send_notify_to_homeowners',
            'notify_with' => array(
                'sms' => t('realEstate_approvedEstate__COODE__'),
                'email' => 'real-estate/notify/approved-email-template'
            ),
        ),
        'real_estate_payment_all_info_for_homeowners' => array(//zamani ke shakhsi ettelaate melk ro kharid baraye sahebe melk notify bere
            'label' => 'realEstate_payment_all_info',
            'description' => 'realEstate_send_notify_to_homeowners',
            'notify_with' => array(
                'sms' => t('realEstate_payment_view_show_info_msg'),
                'email' => 'real-estate/notify/all-info-for-homeowners-email-template'
            ),
        ),
        'real_estate_validate_special_show_info' => array(//bazgasht az bank baraye melk vije va namayesh ettelaate karbar
            'label' => 'realEstate_validate_for_special_show_info',
            'description' => 'realEstate_send_notify_to_homeowners',
            'notify_with' => array(
                'sms' => t('realEstate_validate_with_refcode'),
                'email' => 'real-estate/notify/validate-special-and-info-estate'
            ),
        ),
        'realestate_expire_soon' => array(//3 rooz ghabl az expire
            'label' => 'REALESTATE_EXPIRE_SOON',
            'description' => 'REALESTATE_EXPIRE_SOON_DESC',
            'notify_with' => array(
                'sms' => 'REALESTATE_EXPIRE_SOON_SMS',
                'email' => 'real-estate/notify/expire-soon'
            ),
        ),
    ),
);