<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace SiteMap;

use Application\Model\Config;
use Cron\API\Cron;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use Sample\Model;
use SiteMap\API\SiteMap;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            getSM('sitemap_event_manager')->onCronRun($e);
        });

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('sitemap_event_manager')->onLoadMenuTypes($e);
        });
    }
}