<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/11/14
 * Time: 2:04 PM
 */
return array(
    'category_item' => array(
        'entityType' => 'category_item',
        'label' => 'Category Items',
        'note' => 'translate category items',
        'table' => 'tbl_category_item',
        'pk' => 'id',
        'fields' => array(
            'itemName' => array(
                'label' => 'Name',
                'type' => 'Text',
                'column_type' => 'varchar(100)',
                'collate' => 'utf8_persian_ci'
            ),
            'itemText' => array(
                'label' => 'Text',
                'type' => 'Textarea',
                'editor' => true,
                'column_type' => 'text'
            ),
        ),
    ),
);

