<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace IPTProductOrder;

use Components\API\Block;
use ECommerce\API\Product;
use Menu\API\Menu;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Form\Fieldset;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);

        $em = StaticEventManager::getInstance();

        $em->attach('ECommerce\API\Product', Product::EVENT_LOAD_EXTRA_TYPES, function (Event $e) {
            Product::$types['ipt_system_module'] = 'IPT System Module';
        });

        $em->attach('ECommerce\API\Product', Product::EVENT_LOAD_EXTRA_TYPES_CONFIGS, function (Event $e) {
            /* @var $container \Zend\Form\Fieldset */
            $container = $e->getParam('container');

            $config = new Fieldset('ipt_system_module');
            $config->setLabel('IPT System Module');
            $config->setAttribute('class', 'no-border no-bg hidden');
            $config->setAttribute('id', 'product_type_ipt_system_module');

            $container->add($config);
        });
    }
}


