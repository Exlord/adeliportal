<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 9:55 AM
 */

namespace Note\API;


use Note\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Note',
                    'route' => 'route:admin/note',
                    'child_route' => array(
                        array(
                            'label' => 'View Others Notes',
                            'note' => 'view other peoples notes',
                            'route' => Module::NOTE_VIEW_ALL,
                        ),
                        array(
                            'label' => 'New',
                            'route' => Module::NOTE_NEW,
                            'child_route' => array(
                                array(
                                    'label' => 'Add note to all',
                                    'route' => Module::NOTE_NEW_ALL,
                                    'child_route' => array()
                                ),
                            )
                        ),
                        array(
                            'label' => 'Edit',
                            'route' => Module::NOTE_EDIT,
                            'child_route' => array(
                                array(
                                    'label' => 'Edit Others Notes',
                                    'note' => 'edit other peoples notes',
                                    'route' => Module::NOTE_EDIT_ALL,
                                    'child_route' => array()
                                ),
                            )
                        ),
                        array(
                            'label' => 'Delete',
                            'route' => Module::NOTE_DELETE,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete Others Notes',
                                    'note' => 'delete other peoples notes',
                                    'route' => Module::NOTE_DELETE_ALL,
                                    'child_route' => array()
                                ),
                            )
                        ),
                    ),
                ),
            ),
        );

        return $dataItemAcl;
    }
} 