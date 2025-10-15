<?php
namespace ContentSharing\Controller;

use DataView\Lib\DataGrid;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;

class ContentSharingController extends BaseAbstractActionController
{
    public function configAction()
    {
        $config =getSM('config_table')->getByVarName('content_sharing_config');
        $form = new \ContentSharing\Form\Config();
        $form->setAction(url('admin/content-sharing/config'));
        $form = prepareConfigForm($form);
        if (!empty($config->varValue))
            $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    getSM('config_table')->save($config);
                    db_log_info("Content Sharing Configs changed");
                    $this->flashMessenger()->addInfoMessage('Content Sharing configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('content-sharing/content-sharing/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;

    }
}
