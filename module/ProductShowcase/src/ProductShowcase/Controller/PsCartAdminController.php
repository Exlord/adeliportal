<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ProductShowcase\Controller;

use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use ProductShowcase\Model\PsCartTable;
use ProductShowcase\Model\PsTable;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class PsCartAdminController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('ps_cart_table');
        $grid->route = 'admin/product-showcase/orders';
        if (!isAllowed(\ProductShowcase\Module::ADMIN_PRODUCT_SHOWCASE_ORDERS_ALL))
            $grid->getSelect()->where(array('userId' => current_user()->id));
        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('name', 'Name');
        $date = new Date('createDate', 'Date');
        $view = new Button('View', function (Button $col) {
            $col->route = 'app/product-showcase/cart/view';
            $col->routeParams['orderId'] = $col->dataRow->id;
            $col->contentAttr['target'][] = '_blank';
            $col->contentAttr['class'][] = 'btn btn-default btn-xs';
            $col->icon = 'glyphicon glyphicon-eye-open';
        }, array(
            'headerAttr' => array(),
            'contentAttr' => array(
                'title' => 'View'
            ),
        ));

        $del = new DeleteButton();

        $grid->addColumns(array($id, $title, $date, $view, $del));
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('product-showcase/ps-cart-admin/index');
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
     * @return PsCartTable
     */
    private function getTable()
    {
        return getSM('ps_cart_table');
    }
}
