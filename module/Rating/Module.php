<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Rating;

use Rating\Model;
use System\Module\AbstractModule;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const APP_RATING = 'route:app/rating';
    const APP_RATING_NEW = 'route:app/rating/new';
    const APP_NP = 'route:app/negative-positive-rate';
    const APP_NP_NEW = 'route:app/negative-positive-rate/new';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
    }
}