<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/11/14
 * Time: 2:04 PM
 */
return array(
    'menu_item' => array(
        'label' => 'Menu Item',
        'note' => 'translate menu items label and title',
        'table' => 'tbl_menu_item',
        'pk' => 'id',
        'fields' => array(
            'itemName' => array(
                'label' => 'Name',
                'type' => 'Text',
                'column_type' => 'varchar(200)',
                'collate' => 'utf8_persian_ci'
            ),
            'itemTitle' => array(
                'label' => 'Title',
                'type' => 'Text',
                'column_type' => 'varchar(400)',
                'collate' => 'utf8_persian_ci'
            ),
        ),
    )
);

