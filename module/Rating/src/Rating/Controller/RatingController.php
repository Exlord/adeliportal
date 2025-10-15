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

class RatingController extends BaseAbstractActionController
{
    public function newAction()
    {
        $data = $this->request->getPost()->toArray();
        getSM('rating_table')->rate(
            $data['entityId'],
            $data['entityType'],
            $data['userId'],
            $data['rateScore'],
            $data['entityId2']
        );

        return new JsonModel(array(
            'status' => 1
        ));
    }
}
