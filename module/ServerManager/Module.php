<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace ServerManager;

use System\Module\AbstractModule;
use User\Permissions\Acl\Acl;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;
}