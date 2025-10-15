<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Sms;

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

    const ADMIN_SMS = 'route:admin/sms';
    const ADMIN_SMS_CONFIG = 'route:admin/sms/config';
    const ADMIN_SMS_SEND = 'route:admin/sms/send-sms';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
    }
}