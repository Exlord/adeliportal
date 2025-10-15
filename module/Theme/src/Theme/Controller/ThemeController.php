<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Theme\Controller;

use Application\API\App;
use Application\Model\Config;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use Theme\API\Table;
use Theme\API\Themes;
use Theme\Model\ThemeTable;
use Zend\View\Model\JsonModel;

class ThemeController extends BaseAbstractActionController
{
    public function indexAction()
    {
        //all the public and specific themes in this system
        $all_themes = $this->getApi()->getTemplates();
        //all the themes available for this system
        $themes = $this->getTable()->getAll();
//        $headers = array(
//            'Theme',
//            array('data' => t('Type'), 'align' => 'center'),
//            array('data' => t('Status'), 'align' => 'center'),
//            array('data' => t('Preview'), 'align' => 'center'),
//        );

        foreach ($themes as $row) {
            $status = false;
            if ($row->type == Themes::TYPE_CLIENT && $row->default == 1)
                $status = Themes::DEFAULT_CLIENT;
            elseif ($row->type == Themes::TYPE_ADMIN && $row->default == 1)
                $status = Themes::DEFAULT_ADMIN;
            if ($status)
                $all_themes[$row->name]['status'] = $status;
        }

        $this->viewModel->setVariables(
            array(
                'themes' => $all_themes
            ));
        $this->viewModel->setTemplate('theme/theme/index');
        return $this->viewModel;
    }

    public function setDefaultAction()
    {
        //all the public and specific themes in this system
        $all_themes = $this->getApi()->getTemplates();
        $template = $this->params()->fromRoute('name', false);
        if ($template) {
            if ($all_themes[$template]['status'] != Themes::NOT_AVAILABLE) {
                $this->getTable()->removeDefault(Themes::$types[$all_themes[$template]['type']]);
                $this->getTable()->setDefault($template);
            }
        } else
            return $this->invalidRequest('admin/themes');
        if ($this->getRequest()->isXmlHttpRequest()) {
            if (Themes::$types[$all_themes[$template]['type']] == 'admin')
                $callback = 'window.location="%s"';
            else
                $callback = 'System.Pages.ajaxLoad("%s")';
            
            return new JsonModel(array(
                'callback' => sprintf($callback, url('admin/themes'))
            ));
        }
        return $this->indexAction();
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = getConfig('theme');
        $form = prepareConfigForm(new \Theme\Form\Config());
        $form->setData($config->varValue);

        $layout = 'null';
        if (isset($config->varValue['layout']) && !empty($config->varValue['layout']))
            $layout = $config->varValue['layout'];

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $post = $form->getData();
                    $config->setVarValue($post);
                    $this->getServiceLocator()->get('config_table')->save($config);
                    getCache(true)->removeItem('layout_block_positions');
                    db_log_info("Theme Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Theme configs saved successfully');
                }
            }

            if (isset($post['layout']) && !empty($post['layout']))
                $layout = $post['layout'];
        }


        $this->viewModel->setTemplate('theme/theme/config');
        $this->viewModel->setVariables(array('form' => $form, 'layout' => $layout));
        return $this->viewModel;
    }

    /**
     * @return ThemeTable
     */
    private function getTable()
    {
        return getSM('theme_table');
    }

    /**
     * @return Themes
     */
    private function getApi()
    {
        return getSM('theme_api');
    }
}
