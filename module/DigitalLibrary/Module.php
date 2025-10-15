<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace DigitalLibrary;

use Components\API\Block;
use DigitalLibrary\Model;
use SiteMap\API\SiteMap;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('dl_event_manager')->onLoadBlockConfigs($e);
        });

        $em->attach('SiteMap\API\SiteMap', SiteMap::GENERATING, function (Event $e) {
            getSM('dl_event_manager')->OnSiteMapGeneration($e);
        });
    }
}