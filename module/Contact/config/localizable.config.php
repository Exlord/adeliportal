<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/11/14
 * Time: 2:04 PM
 */
return array(
    'contact' => array(
        'label' => 'Contact Us',
        'note' => 'translate contact users name, address and role',
        'table' => 'tbl_contact_user',
        'pk' => 'id',
        'where' => array(
            'status' => 1
        ),
        'fields' => array(
            'name' => array(
                'label' => 'Name',
                'type' => 'Text',
                'column_type' => 'varchar(300)',
            ),
            'address' => array(
                'label' => 'Address',
                'type' => 'Textarea',
                'column_type' => 'varchar(500)',
            ),
            'role' => array(
                'label' => 'Role',
                'type' => 'Text',
                'column_type' => 'varchar(300)',
            ),
            'description' => array(
                'label' => 'Description',
                'type' => 'Textarea',
                'column_type' => 'text',
                'editor' => true,
            ),
        ),
    ),
    'contact_type' => array(
        'label' => 'Contact Us Types',
        'note' => 'translate contact types title',
        'table' => 'tbl_contact_type',
        'pk' => 'id',
        /*'where' => array(
            'status' => 1
        ),*/
        'fields' => array(
            'title' => array(
                'label' => 'Title',
                'type' => 'Text',
                'column_type' => 'varchar(200)',
            ),
        ),
    ),
);

