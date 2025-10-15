<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CustomersClub\Controller;

use CustomersClub\Form\Config;
use DataView\Lib\DataGrid;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;

class Club extends BaseAbstractActionController
{
    public function indexAction()
    {
        return $this->forward()->dispatch('CustomersClub\Controller\Point');
        $this->viewModel->setTemplate('customers-club/club/index');
        return $this->viewModel;
    }

    /**
     * Admin config page
     * @return ViewModel
     */
    public function configAction()
    {
        $baseConfig = $this->getAPI()->loadBaseConfig();

        $config = getConfig('customers-club');
        $form = prepareConfigForm(new Config($baseConfig));
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            if ($this->isSubmit()) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData();
                    unset($data['modules']['Payment']['Amount']['add_more_select_option']);
                    if (isset($data['modules']['Payment']['Amount']['values']) && is_array($data['modules']['Payment']['Amount']['values'])) {
                        foreach ($data['modules']['Payment']['Amount']['values'] as $key => &$params)
                            unset($params['drop_collection_item']);

                    }
                    $config->setVarValue($data);
                    $this->getConfigTable()->save($config);
                    db_log_info("Customers club configs changed");
                    $this->flashMessenger()->addSuccessMessage('Customers club configs saved successfully');
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form));
        $this->viewModel->setTemplate('customers-club/club/config');
        return $this->viewModel;
    }

    /**
     * @return \CustomersClub\API\Club
     */
    private function getAPI()
    {
        return getSM('cc_api');
    }
}
