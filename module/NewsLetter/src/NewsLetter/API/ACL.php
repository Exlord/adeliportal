<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 9:54 AM
 */

namespace NewsLetter\API;


use NewsLetter\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'News Letter Section',
                    'note' => 'News Letter Section',
                    'route' => Module::ADMIN_NEWS_LETTER,
                    'child_route' => array(
                        array(
                            'label' => 'Send',
                            'note' => 'Send',
                            'route' => Module::ADMIN_NEWS_LETTER_SEND,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'EMails',
                            'note' => '',
                            'route' => Module::ADMIN_NEWS_LETTER_EMAILS_LIST,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Configs',
                            'note' => '',
                            'route' => Module::ADMIN_NEWS_LETTER_CONFIG,
                            'child_route' => array(
                                array(
                                    'label' => 'Configs More',
                                    'note' => '',
                                    'route' => Module::ADMIN_NEWS_LETTER_CONFIG_MORE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Global Configs',
                                    'note' => '',
                                    'route' => Module::ADMIN_NEWS_LETTER_CONFIG_GLOBAL,
                                    'child_route' => ''
                                ),
                            )
                        ),
                        array(
                            'label' => 'Templates Section',
                            'note' => 'News Letter Templates Section',
                            'route' => Module::ADMIN_NEWS_LETTER_TEMPLATE,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_NEWS_LETTER_TEMPLATE_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_NEWS_LETTER_TEMPLATE_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_NEWS_LETTER_TEMPLATE_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_NEWS_LETTER_TEMPLATE_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                    ),
                )
            ),
        );


        return $dataItemAcl;
    }
} 