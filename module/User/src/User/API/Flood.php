<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/20/2014
 * Time: 1:18 PM
 */

namespace User\API;


use Localization\API\Date;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class Flood
{
    public static $FloodCount = 0;
    public static $LastFailTime = 0;
    public static $FloodThreshHoldSeconds = 0;
    public static $Message = null;

    public static function IsFlooded()
    {
        ///TODO get thresh hold from config
        $floodThreshHold = strtotime('-30 minutes');
        self::$FloodThreshHoldSeconds = $floodThreshHoldSeconds = 1800;//30 minutes
        $miniFloodThreshHold = strtotime('-5 minutes');
        $miniFloodThreshHoldSeconds = 300;//5 minutes
        $now = time();

        $attempts = getSM('user_flood_table')->getFailedAttempts();
        if ($attempts && $attempts->count()) {
            $attempts = $attempts->toArray();

            $firstA = end($attempts);
            $lastA = current($attempts);
            $count = count($attempts);


            //more than $floodThreshHold has passed since last failed attempt so we are good to go
            if ($lastA['timestamp'] < $floodThreshHold)
                return false;

            //there has been 5 time flooding in the last 35 min
            if ($count == 5 && $lastA['timestamp'] - $firstA['timestamp'] < $miniFloodThreshHoldSeconds)
                return self::GetFloodedView($floodThreshHoldSeconds - ($now - $lastA['timestamp']));


            //ok lets see how many attempts exist in the last 5 minutes
            //is there any flooding in the last 5 minutes ?
            foreach ($attempts as $row) {
                if ($row['timestamp'] > $miniFloodThreshHold) {
                    self::$LastFailTime = $row['timestamp'];
                    self::$FloodCount++;
                }
            }
        }

        return false;
    }

    public static function RenderMessage(AbstractActionController $controller)
    {
        if (self::$FloodCount > 0) {
            $floodCount = 5 - self::$FloodCount;
            if ($floodCount)
                $controller->flashMessenger()->addWarningMessage(
                    sprintf(t('The login system will be locked after %s failed attempts.'), $floodCount));

        }

    }

    public static function GetFloodedView($unlock_after = null)
    {
        if ($unlock_after == null)
            $unlock_after = Flood::$FloodThreshHoldSeconds;

        $view = new ViewModel();
        $view->setTemplate('user/user/flooded');
        $view->setVariable('unlock_after', Date::formatInterval($unlock_after));
        return $view;
    }
} 