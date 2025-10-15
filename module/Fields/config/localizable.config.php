<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/11/14
 * Time: 2:04 PM
 */
return array(
    'fields' => array(
        'entityType' => 'fields',
        'label' => 'Fields',
        'note' => 'translate dynamic fields',
        'table' => 'tbl_fields',
        'pk' => 'id',
        'fields' => array(
            'fieldName' => array(
                'label' => 'Name',
                'type' => 'Text',
                'column_type' => 'varchar(200)',
            ),
            'fieldDefaultValue' => array(
                'label' => 'Default Value',
                'type' => 'Textarea',
                'editor' => true,
                'column_type' => 'text'
            ),
            'fieldPrefix' => array(
                'label' => 'Prefix',
                'type' => 'Text',
                'column_type' => 'varchar(200)'
            ),
            'fieldPostfix' => array(
                'label' => 'Postfix',
                'type' => 'Textarea',
                'editor' => true,
                'column_type' => 'text'
            ),
            'fieldDisplayTemplate' => array(
                'label' => 'Display Template',
                'type' => 'Textarea',
                'editor' => true,
                'column_type' => 'text'
            ),
        ),
    ),
);

