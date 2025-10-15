<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Thumbnail;

use System\Module\AbstractModule;
use Thumbnail\API\Thumbnail;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function getAutoloaderConfig()
    {
        $loader = parent::getAutoloaderConfig();
        $loader['Zend\Loader\StandardAutoloader']['namespaces']['Imagine'] = $this->getDir() . '/src/Imagine';
        return $loader;
    }
}


