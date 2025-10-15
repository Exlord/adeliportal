<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ProductShowcase\Controller;

use Application\API\Breadcrumb;
use DataView\Lib\DataGrid;
use ProductShowcase\Model\PsCartTable;
use ProductShowcase\Model\PsTable;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class PsCartController extends BaseAbstractActionController
{
    public function cartAction()
    {
        $psCookie = $_COOKIE['productsCookie'];
        if ($psCookie) {
            $psCookie = explode(',', $psCookie);
            $psIds = array();
            foreach ($psCookie as $val)
                $psIds[$val] = 0;
            $country_list = $this->getCountryTable()->getArray();
            $state_list = array();
            $city_list = array();
            $countryId = $this->params()->fromPost('countryId', false);
            if ($countryId)
                $state_list = getSM()->get('city_table')->getArray($countryId);
            $stateId = $this->params()->fromPost('stateId', false);
            if ($stateId)
                $city_list = getSM()->get('city_table')->getArray($stateId);
            $form = new \ProductShowcase\Form\PsCart($country_list, $state_list, $city_list);
            $model = new \ProductShowcase\Model\PsCart();
            $form->setAction(url('app/product-showcase/cart'));
            $form->bind($model);

            if ($this->request->isPost()) {
                $post = $this->request->getPost()->toArray();
                if ($this->isSubmit()) {
                    $form->setData($post);

                    if ($form->isValid()) {

                        if (isset($post['psData']) && $post['psData'])
                            $model->psData = serialize($post['psData']);

                        $model->createDate = time();
                        $model->userId = current_user()->id;

                        $id = $this->getTable()->save($model);
                        $this->flashMessenger()->addSuccessMessage(sprintf(t('PS_NEW_CART_SUCCESS_WITH_CODE'), $id));

                        //send notify

                        if ($this->isSubmitAndClose())
                            return $this->redirect()->toRoute('app/product-showcase');
                        elseif ($this->isSubmitAndNew()) {
                            $model = new \ProductShowcase\Model\PsCart();
                            $form->bind($model);
                        }
                    } else {
                        $this->formHasErrors();
                    }

                } elseif ($this->isCancel()) {
                    return $this->redirect()->toRoute('app/product-showcase');
                }
            }

            $this->viewModel->setVariables(array('error' => false, 'form' => $form, 'psIds' => $psIds));
        } else {
            $this->viewModel->setVariables(array('error' => true));
        }
        $this->viewModel->setTemplate('product-showcase/ps-cart/cart');
        return $this->viewModel;
    }

    public function viewAction()
    {
        $orderId = $this->params()->fromRoute('orderId', null);
        if ($orderId) {
            $select = $this->getTable()->get($orderId);
            if ($select) {
                $this->viewModel->setTemplate('product-showcase/ps-cart/view');
                $this->viewModel->setVariables(array(
                    'select' => $select,
                ));
                return $this->viewModel;
            }
        }
        return $this->invalidRequest('admin/product-showcase');
    }

    /**
     * @return PsCartTable
     */
    private function getTable()
    {
        return getSM('ps_cart_table');
    }
}
