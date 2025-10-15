<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Logger\Controller;

use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\Visualizer;
use System\Controller\BaseAbstractActionController;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\NotIn;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;
use DataView\API\Grid;
use DataView\API\GridColumn;

class LoggerController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $type = $this->params()->fromRoute('type', false);

        $grid = new DataGrid('logger');
        $grid->route = 'admin/reports/logs';
        if ($type)
            $grid->routeOptions['query']['grid_filter_priority'] = $type;

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $priority_select_filter = array(
            LOGGER_ALERT => 'Alert',
            LOGGER_CRIT => 'Critical',
            LOGGER_EMERG => 'Emergency',
            LOGGER_NOTICE => 'Notice',
            LOGGER_ERR => 'Error',
            LOGGER_INFO => 'Information',
            LOGGER_DEBUG => 'Debug',
        );
        $priority = new Visualizer('priority', 'Type', array(), $priority_select_filter);
        $priority->selectFilterData = $priority_select_filter;
//        $priority->lockedValue = 6;

        $message = new Column('message', 'Message');
        $time = new Date('timestamp', 'Date');

        $userId = new Button('User', function (Button $col) {
                $col->route = 'admin/users/view';
                $col->routeParams['id'] = $col->dataRow->uid;
                $col->text = $col->dataRow->username;
            },
            array(
                'contentAttr' => array(
                    'class' => array('ajax_page_load')
                )
            ));

        $del = new DeleteButton();

        $grid->addColumns(array($id, $priority, $time, $userId, $del, $message));
        $grid->setSelectFilters(array($priority));
        $grid->addDeleteSelectedButton();

        $grid->getSelect()
            ->join(array('u' => 'tbl_users'), $grid->getTableGateway()->table . '.uid=u.id', array('username'), 'LEFT')
            ->order('timestamp DESC');


        $this->viewModel->setTemplate('logger/logger/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getTable()->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    /**
     * @return ReaderTable
     */
    private function getTable()
    {
        return getSM('logger');
    }
}
