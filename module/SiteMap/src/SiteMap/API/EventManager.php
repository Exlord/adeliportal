<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 4:12 PM
 */

namespace SiteMap\API;


use Cron\API\Cron;
use Menu\Form\MenuItem;
use Zend\EventManager\Event;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['site-map'] = array(
            'label' => t('SITEMAP'),
            'note' => t('SITEMAP_DESC'),
            'params' => array(array('route' => 'app/sitemap')),
        );
    }

    public function onCronRun(Event $e)
    {
        $last_run = $e->getParam('last_run');
        $start = microtime(true);
        $interval = '+ 24 hours';
        $last = @$last_run->varValue['SiteMap_last_run'];

        if (Cron::ShouldRun($interval, $last)) {

            /* @var $sitemap SiteMap */
            $sitemap = getSM('sitemap_api');
            $sitemap->Generate();
            db_log_info(sprintf(t('Sitemap generated in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));

            $last_run->varValue['SiteMap_last_run'] = time();
        }
    }
} 