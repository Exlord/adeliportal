<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 9:52 AM
 */

namespace Mail\API;


use Mail\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Quick send mail Section',
            'note' => '',
            'route' => Module::APP_QUICK_SEND_MAIL,
            'child_route' => ''
        );

        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'route' => 'route:admin/configs',
                    'child_route' => array(
                        array(
                            'label' => 'Emails',
                            'note' => 'Emails',
                            'route' => Module::ADMIN_MAIL_CONFIGS_MAIL,
                            'child_route' => ''
                        ),
                    ),
                ),
                array(
                    'label' => 'Mails Section',
                    'note' => 'Mails Section',
                    'route' => Module::ADMIN_MAIL,
                    'child_route' => array(
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_MAIL_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Archive Mails Section',
                            'note' => 'Archive Mails Section',
                            'route' => Module::ADMIN_MAIL_ARCHIVE,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_MAIL_ARCHIVE_DELETE,
                                    'child_route' => ''
                                )
                            )
                        ),
                    ),
                ),
            ),
        );


        return $dataItemAcl;
    }
} 