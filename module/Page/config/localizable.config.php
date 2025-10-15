<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/11/14
 * Time: 2:04 PM
 */
$fields = array(
    'pageTitle' => array(
        'label' => 'Title',
        'type' => 'Text',
        'column_type' => 'varchar(300)'
    ),
    'fullText' => array(
        'label' => 'Content',
        'type' => 'Textarea',
        'editor' => true,
        'column_type' => 'text'
    ),
    'introText' => array(
        'label' => 'Intro Text',
        'type' => 'Textarea',
        'editor' => true,
        'column_type' => 'text',
        'visible' => false
    ),
);
$config = array(
    //if 2 entityType has the same original table they both should have a
    //'entityType' key with value equal too of the entities
    //and they both should have shared fields list
    //unset visible=false to show the unshared fields for each entityType
    'page' => array(
        'entityType' => 'page',
        'label' => 'Static Page',
        'note' => 'translate static page content and title',
        'table' => 'tbl_page',
        'pk' => 'id',
        'where' => array(
            'isStaticPage' => 1
        ),
        'fields' => $fields,
    ),
    'content' => array(
        'entityType' => 'page',
        'label' => 'Content',
        'note' => 'translate contents, articles, news and ... content, intro and title',
        'table' => 'tbl_page',
        'pk' => 'id',
        'where' => array(
            'isStaticPage' => 0
        ),
        'fields' => $fields,
    )
);
unset($config['content']['fields']['introText']['visible']);
return $config;

