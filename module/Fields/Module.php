<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Fields;

use System\Module\AbstractModule;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const ADMIN_FIELDS = 'route:admin/fields';
    const ADMIN_FIELDS_NEW = 'route:admin/fields/new';
    const ADMIN_FIELDS_EDIT = 'route:admin/fields/edit';
    const ADMIN_FIELDS_UPDATE = 'route:admin/fields/update';
    const ADMIN_FIELDS_DELETE = 'route:admin/fields/delete';
}
