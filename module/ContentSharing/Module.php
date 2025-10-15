<?php
namespace ContentSharing;

use Application\Model\Config;
use Cron\API\Cron;
use Sample\Model;
use System\Module\AbstractModule;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const ADMIN_CONTENT_SHARING = 'route:admin/content-sharing';
    const ADMIN_CONTENT_SHARING_CONFIG = 'route:admin/content-sharing/config';
}


