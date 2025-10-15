<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/16/14
 * Time: 2:56 PM
 */

namespace Localization\Controller;


use Localization\Form\Config;
use System\Controller\BaseAbstractActionController;

class Localization extends BaseAbstractActionController{
    public function configAction()
    {
        $config = getConfig('localization_config');
        $form = new Config();
        $form = prepareConfigForm($form);
        if (!empty($config->varValue))
            $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Localization Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Localization configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('localization/localization/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }
} 