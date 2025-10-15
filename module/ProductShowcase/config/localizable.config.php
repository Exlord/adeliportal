<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/11/14
 * Time: 2:04 PM
 */
$config = array(
    'product_showcase' => array(
        'entityType' => 'product_showcase',
        'label' => 'PS_PRODUCT_SHOWCASE',
        'note' => 'translate product showcase static fields',
        'table' => 'tbl_product_showcase',
        'pk' => 'id',
        'fields' => array(
            'title' => array(
                'label' => 'Title',
                'type' => 'Text',
                'column_type' => 'varchar(250)',
            ),
        ),
    ),

);
return $config;

