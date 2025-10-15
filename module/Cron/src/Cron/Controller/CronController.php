<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Cron\Controller;

use Cron\API\Cron;
use System\Controller\BaseAbstractActionController;
use Zend\Console\Request as ConsoleRequest;

class CronController extends BaseAbstractActionController
{
    public function cronAction()
    {
        $forceRun = $this->params()->fromQuery('force', false);
        set_time_limit(0);
        $start = microtime(true);
        $cron_last_run = getConfig('cron_last_run');
        $interval = '+ 3 hours';

        $isRunning = false;//isset($cron_last_run->varValue['is_running']) && $cron_last_run->varValue['is_running'] == 1 ? true : false;

        if (!$isRunning || $forceRun) {
            $last = isset($cron_last_run->varValue['cron']) ? $cron_last_run->varValue['cron'] : 0;
            $next = $last ? strtotime($interval, $last) : time();

            if ($next <= time() || $this->request instanceof \Zend\Http\Request) {

                try {
                    $cron_last_run->varValue['is_running'] = 1;
                    saveConfig($cron_last_run);
                    $cron_last_run->varValue = unserialize($cron_last_run->varValue);
                    $this->getCronApi()->runCron($cron_last_run);
                } catch (\Exception $ex) {
                    db_log_exception($ex);
                    print("<pre>" . ___exception_trace($ex) . "</pre>");
//                    if ($this->request instanceof \Zend\Http\Request)
//                        throw $ex;
                }

                $cron_last_run->varValue['is_running'] = 0;
                $cron_last_run->varValue['trying_to_run'] = 0;
                $cron_last_run->varValue['cron'] = time();
                $timeSpan = round((microtime(true) - $start), 4);
                $cron_last_run->varValue['time_span'] = $timeSpan;
                saveConfig($cron_last_run);

                db_log_info(sprintf(t('Cron run completed within %s seconds at %s.'), $timeSpan, dateFormat(time(), 0, 0)));
                if ($this->request instanceof \Zend\Http\Request)
                    return $this->getViewModel(sprintf(t('Cron run completed within %s seconds.'), $timeSpan));

                return sprintf('Cron run completed within %s seconds.', $timeSpan);

            } else {
                if ($this->request instanceof \Zend\Http\Request)
                    return $this->getViewModel(sprintf(('Last cron run was less than %s ago'), $interval));
                return sprintf('Last cron run was less than %s ago', $interval);
            }
        } else {
            if ($this->request instanceof \Zend\Http\Request)
                return $this->getViewModel(t('Another instance of cron is running'));
            return sprintf('Another instance of cron is running');
        }

    }

    private function getViewModel($message)
    {
        $this->viewModel->setVariable('message', $message);
        $this->viewModel->setTemplate('cron/cron/cron');
        return $this->viewModel;
    }

    /**
     * @return Cron
     */
    private function getCronApi()
    {
        return getSM('cron_api');
    }
}


//class BackgroundExec
//{
//    /**
//     * close a client HTTP connection
//     * and start (continue) script execution in background
//     *
//     * @return void
//     */
//    public static function start()
//    {
//        try {
//            ob_end_clean();
//            header('Connection: close;');
//            header('Content-Encoding: none;');
//            ignore_user_abort(true); // optional
//            ob_start();
//            //echo ('Text user will see');
//            $size = ob_get_length();
//            header("Content-Length: $size;");
//            ob_end_flush(); // Strange behaviour, will not work
//            flush(); // Unless both are called !
//            ob_end_clean();
//            //echo('Text user will never see');
//        } catch (\Exception $e) {
//        }
//    }
//}