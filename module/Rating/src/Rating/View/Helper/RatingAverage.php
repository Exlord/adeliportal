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

class RatingAverage extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke($entityId, $entityType, $inPanel = false)
    {
        /* @var $ratingTable  \Rating\Model\ratingTable */
        $ratingTable = getSM()->get('rating_table');
        $selectAvg = $ratingTable->getAverageRate($entityId, $entityType);
        if ($selectAvg['rateScore'])
            $average = round($selectAvg['rateScore'], 2);
        else
            $average = 0;

        return $this->view->render('rating/rating/average', array('score' => $average, 'inPanel' => $inPanel));
    }

}