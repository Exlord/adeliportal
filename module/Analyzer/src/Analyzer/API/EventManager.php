<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 11:29 AM
 */

namespace Analyzer\API;


use Analyzer\Form\SystemStatusBlock;
use Application\Model\Config;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Zend\EventManager\Event;

class EventManager
{
    public function onCronRun(Config $last_run)
    {
        $start = microtime(true);
        $interval = '+ 12 hours';
        //$interval = get your own modules cron interval config
        $last = @$last_run->varValue['Analyzer_last_run'];

        if (Cron::ShouldRun($interval, $last)) {
            /* @var $analyzer Analyzer */
            $analyzer = getSM('analyzer_api');
            $analyzer->archive();
            db_log_info(sprintf(t('Analyzer Archiver cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue['Analyzer_last_run'] = time();
            getCache()->removeItem('all_visits_counts');
        }
    }

    public function onLoadBlockConfigs(Event $e)
    {
        $type = $e->getParam('type');

        if ($type == 'system_status_block') {
            /* @var $form NewBlock */
            $form = $e->getParam('form');

            $dataFieldset = $form->get('data');
            $dataFieldset->setLabel('System Status Block Setting');
            $dataFieldset->add(new SystemStatusBlock());
        }
    }
} 