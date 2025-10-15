<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Cron;

use Application\API\App;
use Sample\Model;
use System\Module\AbstractModule;
use System\Request;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;
use Zend\Console\Request as ConsoleRequest;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $sm = $e->getApplication()->getServiceManager();

        $em = StaticEventManager::getInstance();
        $em->attach('Zend\Mvc\Application', MvcEvent::EVENT_FINISH, function (MvcEvent $e) {

            //this is not a cron run
            if (!($e->getRequest() instanceof ConsoleRequest)) {

                if ($routeMatch = $e->getRouteMatch()) {
                    $routeName = $routeMatch->getMatchedRouteName();
                    //this is not a cron run
                    if (strpos($routeName, 'cron') === false) {
                        $cron_last_run = getConfig('cron_last_run');
                        $interval = '+1 month';

                        $trying_to_run = isset($cron_last_run->varValue['trying_to_run']) && $cron_last_run->varValue['trying_to_run'] == 1 ? true : false;
                        if (!$trying_to_run) {
                            //cron is not running
                            $isRunning = isset($cron_last_run->varValue['is_running']) && $cron_last_run->varValue['is_running'] == 1 ? true : false;
                            if (!$isRunning) {
                                $last = isset($cron_last_run->varValue['cron']) ? $cron_last_run->varValue['cron'] : 0;
                                $next = $last ? strtotime($interval, $last) : time();
                                if ($next <= time()) {
                                    $cron_last_run->varValue['trying_to_run'] = 1;
                                    saveConfig($cron_last_run);
                                    $url = App::siteUrl() . url('app/cron');
                                    Request::Async($url);
                                }
                            }
                        }
                    }
                }
            }
        });
    }
}


