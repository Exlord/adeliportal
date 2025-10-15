<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Rating\Controller;

use Rating\Form\Config;
use System\Controller\BaseAbstractActionController;
use Zend\Session\Container;

class RatingAdminController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $this->viewModel->setTemplate('rating/admin/index');
        return $this->viewModel;
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = getSM('config_table')->getByVarName('rating');
        $form = prepareConfigForm(new \Rating\Form\Config());
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    getSM('config_table')->save($config);
                    db_log_info("Rating Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Rating configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('rating/admin/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }
}
