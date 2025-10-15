<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:40 AM
 */

namespace Application\API;


use Application\Module;

class ACL {
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'Print',
            'note' => 'Print',
            'route' => 'route:app/print',
        );

        $dataItemAcl[] = array(
            'label' => 'Access to Management',
            'note' => '',
            'route' => Module::ADMIN,
            'child_route' => array(
                array(
                    'label' => 'Admin Menu',
                    'note' => '',
                    'route' => 'root:admin:menu',
                    'child_route' => ''
                ),
                array(
                    'label' => 'Optimization',
                    'note' => '',
                    'route' => Module::ADMIN_OPTIMIZATION,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Cache',
                    'note' => '',
                    'route' => Module::ADMIN_CACHE,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Backup Section',
                    'note' => 'Backup',
                    'route' => Module::ADMIN_BACKUP,
                    'child_route' => array(
                        array(
                            'label' => 'Backup From Database',
                            'note' => 'Backup From Database',
                            'route' => Module::ADMIN_BACKUP_DB,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_BACKUP_DB_NEW,
                                    'child_route' => ''
                                ),
                                /*array(
                                    'label' => 'Access admin area backup db create',
                                    'note' => 'Access admin area backup db create',
                                    'route' => Module::ADMIN_BACKUP_DB_CREATE,
                                    'child_route' => ''
                                ),*/
                                array(
                                    'label' => 'Restore',
                                    'note' => 'Restore',
                                    'route' => Module::ADMIN_BACKUP_DB_RESTORE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_BACKUP_DB_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Download',
                                    'route' => 'route:admin/backup/db/download',
                                    'child_route' => ''
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'label' => 'Configs Section',
                    'note' => '',
                    'route' => Module::ADMIN_CONFIGS,
                    'child_route' => array(
                        array(
                            'label' => 'System',
                            'note' => 'Configs System',
                            'route' => Module::ADMIN_CONFIGS_SYSTEM,
                            'child_route' => array(
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete Fav icon',
                                    'route' => Module::ADMIN_CONFIGS_SYSTEM_DELETE_FAV_ICON,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete Admin Logo',
                                    'route' => Module::ADMIN_CONFIGS_SYSTEM_DELETE_ADMIN_LOGO,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Dashboard Widget',
                            'note' => 'Dashboard widgets selection',
                            'route' => Module::ADMIN_CONFIGS_WIDGETS,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Captcha',
                            'note' => 'select captcha type and their settings',
                            'route' => 'route:admin/configs/captcha',
                        )
                    ),
                ),
                array(
                    'label' => 'Content',
                    'note' => 'Access Contents Menu',
                    'route' => Module::ADMIN_CONTENTS,
                    'child_route' => ''
                ),
                array(
                    'label' => 'Modules',
                    'note' => '',
                    'route' => Module::ADMIN_MODULES,
                    'child_route' => array(
                        array(
                            'label' => 'Rebuild',
                            'note' => 'Modules Rebuild',
                            'route' => Module::ADMIN_MODULES_REBUILD,
                            'child_route' => ''
                        ),
                    )
                ),
                array(
                    'label' => 'Orders',
                    'note' => '',
                    'route' => 'route:admin/orders',
                ),
                array(
                    'label' => 'Structure',
                    'note' => '',
                    'route' => Module::ADMIN_STRUCTURE,
                ),
                array(
                    'label' => 'Reports',
                    'note' => 'Reports',
                    'route' => Module::ADMIN_REPORTS,
                ),
                array(
                    'label' => 'Templates Section',
                    'note' => 'Templates Section',
                    'route' => Module::ADMIN_MAIL_TEMPLATE,
                    'child_route' => array(
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => Module::ADMIN_MAIL_TEMPLATE_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'Edit',
                            'route' => Module::ADMIN_MAIL_TEMPLATE_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'Delete',
                            'route' => Module::ADMIN_MAIL_TEMPLATE_DELETE,
                            'child_route' => ''
                        )
                    )
                ),
                array(
                    'label' => 'Alias Url',
                    'note' => 'create/edit a list of alias for system urls',
                    'route' => 'route:admin/alias',
                    'child_route' => array(
                        array(
                            'label' => 'New',
                            'note' => 'create alias for system urls',
                            'route' => 'route:admin/alias/new',
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => 'edit alias for system urls',
                            'route' => 'route:admin/alias/edit',
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => 'delete alias for system urls',
                            'route' => 'route:admin/alias/delete',
                        ),
                    )
                ),
                array(
                    'label' => 'Help',
                    'note' => '',
                    'route' => 'route:admin/help',
                ),
            ),
        );
        
            return $dataItemAcl;
    }
} 