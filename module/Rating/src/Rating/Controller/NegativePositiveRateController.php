<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Rating\Controller;

use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;
use DataView\API\Grid;
use DataView\API\GridColumn;

class NegativePositiveRateController extends BaseAbstractActionController
{
    public function indexAction()
    {
        /* @var $ratingTable  \Rating\Model\ratingTable */
        $ratingModel = new \Rating\Model\rating();
        $ratingTable = getSM()->get('rating_table');
        return $this->viewModel;
    }


    public function newAction()
    {//TODO Insert On duplicate entry Update
        //NP = Negative And Positive
        $data = $this->request->getPost()->toArray();
        $data['userId']=current_user()->id;
        $data['date']=time();
        /* $date_session_NP = getSession('RatingNP');
         $dateRate = $date_session_NP->dateForRateNP;

         if (!isset($dateRate[$data['entityId']]) || $dateRate[$data['entityId']] < time() - 300) {*/
        $select = getSM()->get('rating_table')->getAll(array('entityId' => $data['entityId'], 'entityType' => $data['entityType'], 'userId' => $data['userId']))->current();

        if (isset($select->id))
            $data['id'] = $select->id;



        $id = getSM()->get('rating_table')->save($data);
       /* $selectSum = getSM()->get('rating_table')->getSumRate($data['entityId'], $data['entityType']);
        getSM()->get('comment_table')->update(array('rate' => $selectSum['rateScore']), array('id' => $data['entityId']));*/
        //  $date_session_NP->dateForRateNP[$data['entityId']] = time();
        return new JsonModel(array(
           // 'dateRate' => 300,
            'statusNP' => 1
        ));
        //  }

    }
}
