<?php
namespace Contact;
return array(
    'service_manager' => array(
        'invokables' => array(
            'contact_type_table' => 'Contact\Model\ContactTypeTable',
            'contact_user_table' => 'Contact\Model\ContactUserTable',
            'contact_table' => 'Contact\Model\ContactTable',
            'contact_event_manager' => 'Contact\API\EventManager',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Contact\Controller\Contact' => 'Contact\Controller\ContactController',
            'Contact\Controller\ContactUserAdmin' => 'Contact\Controller\ContactUserAdminController',
            'Contact\Controller\ContactUserClient' => 'Contact\Controller\ContactUserClientController',
            'Contact\Controller\Representative' => 'Contact\Controller\RepresentativeController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'contact' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/contact',
                            'defaults' => array(
                                'controller' => 'Contact\Controller\Contact',
                                'action' => 'contacts',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'Contact\Controller\Contact',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                            'representative-config' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/representative-config',
                                    'defaults' => array(
                                        'controller' => 'Contact\Controller\Representative',
                                        'action' => 'config',
                                    ),
                                ),
                            ),
                            'contacts' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/contacts',
                                    'defaults' => array(
                                        'controller' => 'Contact\Controller\Contact',
                                        'action' => 'contacts',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'Contact\Controller\Contact',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'Contact\Controller\Contact',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'user' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/user',
                                    'defaults' => array(
                                        'controller' => 'Contact\Controller\ContactUserAdmin',
                                        'action' => 'index',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'new' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/new',
                                            'defaults' => array(
                                                'controller' => 'Contact\Controller\ContactUserAdmin',
                                                'action' => 'new',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:id/edit',
                                            'defaults' => array(
                                                'controller' => 'Contact\Controller\ContactUserAdmin',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/delete',
                                            'defaults' => array(
                                                'controller' => 'Contact\Controller\ContactUserAdmin',
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                    'update' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/update',
                                            'defaults' => array(
                                                'controller' => 'Contact\Controller\ContactUserAdmin',
                                                'action' => 'update',
                                            ),
                                        ),
                                    ),
                                    'menu-contact-user-list' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/user-list',
                                            'defaults' => array(
                                                'controller' => 'Contact\Controller\ContactUserAdmin',
                                                'action' => 'menu-contact-user-list',
                                            ),
                                        ),
                                    ),
                                    'menu-contact-category-list' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/category-list',
                                            'defaults' => array(
                                                'controller' => 'Contact\Controller\ContactUserAdmin',
                                                'action' => 'menu-contact-category-list',
                                            ),
                                        ),
                                    ),
                                )
                            ),
                        )
                    ),

                ),
            ),
            'app' => array(
                'child_routes' => array(
                    'contact' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/contact',
                            'constraints' => array(),
                            'defaults' => array(
                                'controller' => 'Contact\Controller\ContactUserClient',
                                'action' => 'contacts',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'category' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/custom/:catId',
                                    'defaults' => array(
                                        'controller' => 'Contact\Controller\ContactUserClient',
                                        'action' => 'contacts',
                                    ),
                                ),
                            ),
                            'single' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/single/:contactId',
                                    'defaults' => array(
                                        'controller' => 'Contact\Controller\ContactUserClient',
                                        'action' => 'contacts',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'representative' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/representative',
                            'defaults' => array(
                                'controller' => 'Contact\Controller\Representative',
                                'action' => 'representative',
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'admin_menu' => array(
            'modules' => array(
                'pages' => array(
                    array(
                        'label' => 'PERM_Contact',
                        'route' => 'admin/contact',
                        'resource' => 'route:admin/contact',
                        'pages' => array(
                            array(
                                'label' => 'User',
                                'route' => 'admin/contact/user',
                                'visible' => true,
                                'pages' => array(
                                    array(
                                        'label' => 'New User',
                                        'route' => 'admin/contact/user/new',
                                        'visible' => true
                                    ),
                                )
                            ),
                            array(
                                'label' => 'Messages',
                                'route' => 'admin/contact/contacts',
                                'visible' => true,
                            ),
                        )
                    )
                ),
            ),
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'PERM_Contact',
                        'route' => 'admin/contact/config',
                        'resource' => 'route:admin/contact/config',
                    ),
                    array(
                        'label' => 'Representative',
                        'route' => 'admin/contact/representative-config',
                        'resource' => 'route:admin/contact/representative-config',
                    )
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
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
    'template_placeholders' => array(
        'Contact Us' => array(
            '__NAME__' => 'Name',
            '__EMAIL__' => 'Email',
            '__MOBILE__' => 'Mobile',
            '__DESCRIPTION__' => 'Description',
            '__TYPE__' => 'Type',
            '__DATE__' => 'Date',
            '__GETTER__' => 'Section',
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'Contact' => __DIR__ . '/../public',
            ),
        ),
    ),
);