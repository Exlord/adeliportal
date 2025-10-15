<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace SimpleOrder;

use Menu\API\Menu;
use Menu\Form\MenuItem;
use Sample\Model;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    const ENTITY_TYPE = 'simple_order';
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('simple_order_event_manager')->onLoadMenuTypes($e);
        });
    }
}


