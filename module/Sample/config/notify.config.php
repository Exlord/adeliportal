<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/12/14
 * Time: 1:36 PM
 */
return array(
    'Sample' => array(
        'some_event' => array(
            'label' => 'Some Event',
            'description' => 'Some Event Description',
            'notify_with' => array(
                'sms' => 'some event happened',
                'email' => 'sample/sample/some-event',
                'internal' => 'yooo some event happened'
            ),
            'allow_user_role_override' => true, //default
            'allow_user_override' => false, //default
        )
    )
);