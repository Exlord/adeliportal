<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:47 AM
 */

namespace Contact\API;


use Contact\Module;

class ACL {
    public function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Contact Us Section & Show All Contact User',
            'note' => '',
            'route' => Module::APP_CONTACT,
            'child_route' => array(
                array(
                    'label' => 'Show Contact User By Category Item',
                    'note' => '',
                    'route' => Module::APP_CONTACT_CATEGORY,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Show Single Contact User',
                    'note' => '',
                    'route' => Module::APP_CONTACT_SINGLE,
                    'child_route' => ''
                ),
            ),
        );

        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Contact Us Section',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_CONTACT,
                    'child_route' => array(
                        array(
                            'label' => 'Configs',
                            'note' => '',
                            'route' => Module::ADMIN_CONTACT_CONFIG,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Configs Representative',
                            'note' => '',
                            'route' => Module::ADMIN_CONTACT_CONFIG_REPRESENTATIVE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'View Contacts',
                            'note' => '',
                            'route' => Module::ADMIN_CONTACT_CONTACTS,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete',
                                    'note' => '',
                                    'route' => Module::ADMIN_CONTACT_CONTACTS_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => '',
                                    'route' => Module::ADMIN_CONTACT_CONTACTS_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Member Section',
                            'note' => '',
                            'route' => Module::ADMIN_CONTACT_USER,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => '',
                                    'route' => Module::ADMIN_CONTACT_USER_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => '',
                                    'route' => Module::ADMIN_CONTACT_USER_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => '',
                                    'route' => Module::ADMIN_CONTACT_USER_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => '',
                                    'route' => Module::ADMIN_CONTACT_USER_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'User List For Menu',
                            'note' => '',
                            'route' => Module::ADMIN_CONTACT_USER_MENU_USER_LIST,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Contact us Sections Category List For Menu',
                            'note' => '',
                            'route' => Module::ADMIN_CONTACT_USER_MENU_CATEGORY_LIST,
                            'child_route' => '',
                        ),
                    ),
                )
            ),
        );


            return $dataItemAcl;
    }
} 