<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Koushan
 * Date: 7/23/13
 * Time: 10:41 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Rating\View\Helper;


use Zend\Session\Container;

class NegativeAndPositiveRate extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke($entityId, $entityType)
    {
       // $PermitForUpdateRateNP = true;
        $selectSum = getSM('rating_table')->getSumRate($entityId, $entityType);
        $vote = getSM('rating_table')->getVote($entityId, $entityType);
        $sum = $selectSum['rateScore'];
        if (!$sum)
            $sum = 0;

        $flagShowForGuest = false;
        $config = getSM('config_table')->getByVarName('rating')->varValue;;
        if(isset($config['npGuestStatus']) && $config['npGuestStatus'])
            $flagShowForGuest = $config['npGuestStatus'];


        /*$date_session_NP = getSession('RatingNP');
        if(!$date_session_NP->offsetExists('dateForRateNP'))
            $date_session_NP->dateForRateNP=array();

        $dateRate = $date_session_NP->dateForRateNP;

        if (isset($dateRate[$entityId]) && $dateRate[$entityId] > time() - 300)
            $PermitForUpdateRateNP = false;
        else
            unset($dateRate[$entityId]);

        $date_session_NP->dateForRateNP = $dateRate;*/
        $html = $this->view->render('rating/negative-positive-rate/negative-and-positive-rate', array('ei' => $entityId, 'et' => $entityType ,'userRate' => $selectSum['userRate'], 'sum' => $sum,'vote'=>$vote,'flagShowForGuest'=>$flagShowForGuest /*'PermitForUpdateRateNP' => $PermitForUpdateRateNP*/));
        return $html;
    }

}