<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/20/14
 * Time: 8:58 AM
 */
return array(
    'Contact' => array(
        'Contact_notify' => array(//zamani ke melk jadid sabt shod baraye moshvere amlake sahebe manghate notify bere
            'label' => 'CONTACT_NOTIFY_LABEL',
            'description' => 'CONTACT_NOTIFY_DESC',
            'notify_with' => array(
               // 'sms' => 'realEstate_new_notify_sms_estate_registration_agent_area_with_CODE',
                'email' => 'contact/notify/notify'
            ),
        ),
    ),
);