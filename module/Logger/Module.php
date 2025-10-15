<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */

namespace Logger;

use User\Permissions\Acl\Acl;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;

class Module extends \System\Module\AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const LOGS = 'route:admin/reports/logs';
    const LOGS_DELETE = 'route:admin/reports/logs/delete';
}
