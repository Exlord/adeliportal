<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/23/13
 * Time: 2:32 PM
 */
namespace JQuery;
return array(
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'jquery' => __DIR__ . '/../public',
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/' . __NAMESPACE__ . '.lang',
            ),
        ),
    ),
    'JQuery' => array(
        'name' => 'JQuery',
        'note' => 'jQuery library and other jquery related stuff',
        'can_be_disabled' => false,
        'client_usable' => false,
        'depends' => array('System'),
        'stage' => 'active',
        'version' => '1.0'
    ),
);