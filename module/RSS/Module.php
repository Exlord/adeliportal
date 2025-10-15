<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace RSS;

use Application\Model\Config;
use Components\API\Block;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Menu\Form\MenuItem;
use RSS\Model\ReaderTable;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Form\Fieldset;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const ADMIN_RSS_READER = 'route:admin/rss-reader';
    const ADMIN_RSS_READER_NEW = 'route:admin/rss-reader/new';
    const ADMIN_RSS_READER_EDIT = 'route:admin/rss-reader/edit';
    const ADMIN_RSS_READER_DELETE = 'route:admin/rss-reader/delete';
    const ADMIN_RSS_READER_UPDATE = 'route:admin/rss-reader/update';
    const ADMIN_RSS_READER_CONFIG = 'route:admin/rss-reader/config';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('rss_reader_event_manager')->onLoadBlockConfigs($e);
        });

        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            getSM('rss_reader_event_manager')->onCronRun($e);
        });
    }
}