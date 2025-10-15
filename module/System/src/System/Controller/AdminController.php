<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/19/14
 * Time: 9:54 AM
 */

namespace System\Controller;


use Application\Model\Config;

class AdminController extends BaseAbstractActionController
{
    public function captchaConfigAction()
    {
        /* @var $config Config */
        $config = getConfig('system_captcha');
        $form = new \System\Form\CaptchaConfig();
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
                    db_log_info("System captcha Configs changed");
                    $this->flashMessenger()->addInfoMessage('System captcha configs saved successfully');
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setVariables(array('form' => $form,));
        $this->viewModel->setTemplate('system/admin/captcha-config');
        return $this->viewModel;

    }
} 