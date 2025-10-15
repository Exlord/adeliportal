<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/27/14
 * Time: 11:24 AM
 */

namespace EducationalCenter\Controller;


use EducationalCenter\Form\Config;
use System\Controller\BaseAbstractActionController;

class EducationalCenter extends BaseAbstractActionController
{
    public function indexAction()
    {
        return $this->adminMenuPage();
    }

    public function configAction()
    {
        $config = getConfig('educational-center');
        $form = prepareConfigForm(new Config());
        $form->setData($config->varValue);


        if ($this->request->isPost()) {
            if ($this->isSubmit()) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getConfigTable()->save($config);
                    db_log_info("Educational center Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Educational center configs saved successfully');
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form));
        $this->viewModel->setTemplate('educational-center/config');
        return $this->viewModel;
    }
} 