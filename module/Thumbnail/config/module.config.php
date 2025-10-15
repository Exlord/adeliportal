<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:11 PM
 */
namespace Thumbnail;
return array(
    'service_manager' => array(
        'invokables' => array(
            'thumbnail_api' => 'Thumbnail\API\Thumbnail'
        ),
    ),
    'controllers' => array(
        'invokables' => array(),
    ),
    'router' => array(
        'routes' => array()
    ),
    'navigation' => array(),
    'Thumbnail' => array(
        'name' => 'Thumbnail Module',
        'note' => 'Generates thumbnail for images',
        'can_be_disabled' => false,
        'client_usable' => false,
        'depends' => array('System'),
        'stage' => 'development','version'=>'1.0'
    ),
);