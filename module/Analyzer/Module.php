<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Analyzer;

use Analyzer\API\Analyzer;
use Analyzer\Form\SystemStatusBlock;
use Analyzer\Model;
use Application\API\App;
use Application\Model\Config;
use Components\API\Block;
use Components\Form\NewBlock;
use Cron\API\Cron;
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
        $em->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function (MvcEvent $e) {
            /* @var $analyzer Analyzer */
            $analyzer = getSM('analyzer_api');
            $analyzer->addVisitor();
        }, 100);

        //CRON
        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            $last_run = $e->getParam('last_run');
            getSM('analyzer_event_manager')->onCronRun($last_run);
        });

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e)  {
            getSM('analyzer_event_manager')->onLoadBlockConfigs($e);
        });
    }
}


