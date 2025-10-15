<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/17/14
 * Time: 11:10 AM
 */
return array(
    'EducationalCenter' => array(
        'label' => 'Educational Center',
        'events' => array(
            'Register.Done' => array(
                'label' => 'Class Register',
                'description' => 'how many points the user will earn for registering in a class'
            ),
            'Register.Cancel' => array(
                'label' => 'Class Register Cancel',
                'description' => 'how many points the user will loose for canceling a class registration'
            )
        )
    )
);