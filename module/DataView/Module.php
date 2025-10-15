<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace DataView;

use System\Module\AbstractModule;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;
}
