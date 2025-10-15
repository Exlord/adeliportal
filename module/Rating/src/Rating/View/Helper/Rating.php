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

class Rating extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke($entityId, $entityType, $entityId2 = 0, $showMyVote = 1)
    {
        $PermitForUpdateRate = true;
        /* @var $ratingTable  \Rating\Model\ratingTable */
        $ratingTable = getSM()->get('rating_table');
        $selectAvg = $ratingTable->getAverageRate($entityId, $entityType, $entityId2);
        if ($selectAvg['rateScore'])
            $average = round($selectAvg['rateScore'], 2);
        else
            $average = 0;

        $userVote = $ratingTable->getVote($entityId, $entityType, $entityId2);
        $html = $this->view->render('rating/rating/rating',
            array(
                'ei' => $entityId,
                'ei2' => $entityId2,
                'et' => $entityType,
                'average' => $average,
                'userVote' => $userVote,
                'showMyVote' => $showMyVote
            ));
        return $html;
    }

}