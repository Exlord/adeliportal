<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace IPTProductOrder\Controller;

use DataView\Lib\DataGrid;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;

class OrderController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $categories = $this->getCategoryItemTable()->getItemsByMachineName('commerce_product');
        $parents = array();
        foreach ($categories as $cat) {
            $parents[$cat->parentId][] = $cat;
        }

        $this->viewModel->setTemplate('ipt-product-order/order/index');
        $this->viewModel->setTerminal(true);
        $this->viewModel->setVariables(array(
            'categories' => $parents
        ));
        return $this->viewModel;
    }
}
