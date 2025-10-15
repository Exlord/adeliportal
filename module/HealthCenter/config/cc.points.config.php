<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/17/14
 * Time: 11:10 AM
 */
return array(
    'HealthCenter' => array(
        'label' => 'HealthCenter',
        'events' => array(
            'Reserve.Done' => array(
                'label' => 'Reserve',
                'description' => 'how many points the user will earn for reserving a appointment'
            ),
            'Reserve.Cancel' => array(
                'label' => 'Reserve Cancel',
                'description' => 'how many points the user will loose for canceling a reserved appointment'
            )
        )
    )
);