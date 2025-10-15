<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:06 AM
 */

namespace PhoneBook\API;

use PhoneBook\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Phone Book Section',
                    'note' => 'Phone Book Section',
                    'route' => Module::ADMIN_PHONE_BOOK,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_PHONE_BOOK_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'Edit',
                            'route' => Module::ADMIN_PHONE_BOOK_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_PHONE_BOOK_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update',
                            'note' => 'Update',
                            'route' => Module::ADMIN_PHONE_BOOK_UPDATE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Send Sms',
                            'note' => 'Send Sms',
                            'route' => Module::ADMIN_PHONE_BOOK_SEND_SMS,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Send Email',
                            'note' => 'Send Email',
                            'route' => Module::ADMIN_PHONE_BOOK_SEND_EMAIL,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Word Export',
                            'note' => 'Word Export',
                            'route' => Module::ADMIN_PHONE_BOOK_WORD_EXPORT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Print',
                            'note' => 'Print',
                            'route' => Module::ADMIN_PHONE_BOOK_PRINT,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );

        return $dataItemAcl;
    }
} 