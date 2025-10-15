<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace FormsManager;

use System\Module\AbstractModule;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

   // const ADMIN_FORMS_CONFIGS = 'route:admin/configs';
    const ADMIN_FORMS_CONFIGS_FORMS = 'route:admin/configs/forms';
    const ADMIN_FORMS_DATA = 'route:admin/forms-data';
    const ADMIN_FORMS_DATA_NEW = 'route:admin/forms-data/new';
    const ADMIN_FORMS_DATA_EDIT = 'route:admin/forms-data/edit';
    const ADMIN_FORMS_DATA_EDIT_ALL = 'route:admin/forms-data/edit:all';
    const ADMIN_FORMS_DATA_DELETE = 'route:admin/forms-data/delete';
    const ADMIN_FORMS_DATA_DELETE_ALL = 'route:admin/forms-data/delete:all';
    const ADMIN_FORMS_DATA_VIEW = 'route:admin/forms-data/view';
    const ADMIN_FORMS = 'route:admin/forms';
    const ADMIN_FORMS_NEW = 'route:admin/forms/new';
    const ADMIN_FORMS_DELETE = 'route:admin/forms/edit';
    const ADMIN_FORMS_EDIT = 'route:admin/forms/delete';
}