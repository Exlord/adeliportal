<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/11/14
 * Time: 2:04 PM
 */
return array(
    'dynamic_form' => array(
        'entityType' => 'dynamic_form',
        'label' => 'Form Manager',
        'note' => 'translate dynamic forms',
        'table' => 'tbl_forms',
        'pk' => 'id',
        'fields' => array(
            'title' => array(
                'label' => 'Title',
                'type' => 'Text',
                'column_type' => 'varchar(200)',
                'collate' => 'utf8_persian_ci'
            ),
            'format' => array(
                'label' => 'Format',
                'type' => 'Textarea',
                'editor' => true,
                'column_type' => 'mediumtext'
            ),
        ),
    ),
);

