<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:07 AM
 */

namespace PM\API;


class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'PM',
                    'note' => 'private messages',
                    'route' => 'route:admin/pm',
                    'child_route' => array(
                        array(
                            'label' => 'Send a private message',
                            'note' => '',
                            'route' => 'route:admin/pm/new',
                            'child_route' => array(
                                array(
                                    'label' => 'Multiple Send',
                                    'note' => 'send a single message to multiple users at once by providing their usernames',
                                    'route' => PM::SEND_TO_MULTIPLE,
                                    'child_route' => array(
                                        array(
                                            'label' => 'Send to All',
                                            'note' => 'send a single message to all users at once',
                                            'route' => PM::SEND_TO_ALL,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        array(
                            'label' => 'View private message',
                            'note' => 'View a private message with full details',
                            'route' => 'route:admin/pm/view',
                            'child_route' => array(
                                array(
                                    'label' => 'View All',
                                    'note' => 'view private messages from any user to any user',
                                    'route' => PM::VIEW_ALL,
                                ),
                            )
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }
} 