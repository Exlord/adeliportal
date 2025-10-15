<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Cron\API;

use Application\Model\Config;
use System\API\BaseAPI;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class Cron extends BaseAPI
{
    const CRON_RUN = 'CronRun';

    public static function ShouldRun($interval, $lastRun)
    {
        $next = strtotime($interval, $lastRun);
        return ($next <= time());
    }

    public static function GetRunTime($start)
    {
        return round((microtime(true) - $start), 4);
    }

    public function runCron(Config $cron_last_run)
    {
        $this->getEventManager()->trigger(Cron::CRON_RUN, $this,
            array(
                'last_run' => $cron_last_run
            )
        );
    }
}