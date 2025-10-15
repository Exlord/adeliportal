<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 11:51 AM
 */

namespace Sample\API;


use Application\Model\Config;
use Cron\API\Cron;

class EventManager {
    public function onCronRun(Config $last_run)
    {
        $start = microtime(true);
        $interval = '+ 12 hours';
        //$interval = get your own modules cron interval config
        $last = @$last_run->varValue['[NAMESPACE]_last_run'];

        if (Cron::ShouldRun($interval, $last)) {

            //do your thing here

            db_log_info(sprintf(t('My cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));

            $last_run->varValue['[NAMESPACE]_last_run'] = time();

        }
    }
} 