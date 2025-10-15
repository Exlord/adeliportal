<?php

namespace NewsLetter;

use Cron\API\Cron;
use Mail\API\Mail;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use Sample\Model;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;
use Application\Model\Config;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    const ENTITY_TYPE = 'newsletter';
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const ADMIN_NEWS_LETTER = 'route:admin/news-letter';
    const ADMIN_NEWS_LETTER_SEND = 'route:admin/news-letter/send';
    const ADMIN_NEWS_LETTER_EMAILS_LIST = 'route:admin/news-letter/emails-list';
    const ADMIN_NEWS_LETTER_CONFIG = 'route:admin/news-letter/config';
    const ADMIN_NEWS_LETTER_CONFIG_MORE = 'route:admin/news-letter/config/more';
    const ADMIN_NEWS_LETTER_CONFIG_GLOBAL = 'route:admin/news-letter/config/global-config';
    const ADMIN_NEWS_LETTER_TEMPLATE = 'route:admin/news-letter/template';
    const ADMIN_NEWS_LETTER_TEMPLATE_NEW = 'route:admin/news-letter/template/new';
    const ADMIN_NEWS_LETTER_TEMPLATE_EDIT = 'route:admin/news-letter/template/edit';
    const ADMIN_NEWS_LETTER_TEMPLATE_DELETE = 'route:admin/news-letter/template/delete';
    const ADMIN_NEWS_LETTER_TEMPLATE_UPDATE = 'route:admin/news-letter/template/update';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);

        $em = StaticEventManager::getInstance();

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e){
            getSM('news_letter_event_manager')->onLoadMenuTypes($e);
        });

        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            $lastRun = $e->getParam('last_run');
            getSM('news_letter_event_manager')->onCronRun($lastRun);
        });
    }
}


