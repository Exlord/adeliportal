<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/12/14
 * Time: 1:36 PM
 */
return array(
    'CustomersClub' => array(
        'points' => array(
            'label' => 'Points Received',
            'description' => 'the member has received some points',
            'notify_with' => array(
                'internal' => 'You received __POINT__ points for : __POINT_NOTE__'
            ),
        ),
        'points_edit' => array(
            'label' => 'Points Edited',
            'description' => 'the admin has edited some points',
            'notify_with' => array(
                'internal' => '__ADMIN__ has changed your points for __POINT_NOTE__ from __POINT_BEFORE__ to __POINT_AFTER__'
            ),
        ),
        'points_delete' => array(
            'label' => 'Points Deleted',
            'description' => 'the admin has deleted some points',
            'notify_with' => array(
                'internal' => '__ADMIN__ has deleted your points __POINT__ for __POINT_NOTE__'
            ),
        )
    )
);