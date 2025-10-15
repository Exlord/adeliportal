<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Sample\Controller;

use DataView\Lib\DataGrid;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;

class SampleController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('block_table');
        $grid->route = 'admin/block';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('title', 'Title');
        $description = new Column('description', 'Description');

        $position = new Select('position', 'Position', $theme['positions'],
            array(), array('headerAttr' => array('width' => '50px')));

        $status = new Select('enabled', 'Status',
            array('0' => t('Inactive'), '1' => t('Active')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px')));

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $description, $title, $position, $status, $edit, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('New Block');

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $form = new \RSS\Form\Reader();
        if (!$model) {
            $model = new \Mail\Model\Template();
            $form->setAction(url('admin/mail/template/new'));
        } else {
            $form->setAction(url('admin/mail/template/edit', array('id' => $model->id)));
            $form = prepareForm($form, array('submit-new'));
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {
                    $this->getTable()->save($model);
                    $this->flashMessenger()->addSuccessMessage('Private Message Sent successfully');
                    if ($this->isSubmitAndClose())
                        return $this->indexAction();
                    elseif ($this->isSubmitAndNew()) {
                        $model = new \PM\Model\PM();
                        $form->bind($model);
                    }
                } else
                    $this->formHasErrors();

            } elseif ($this->isCancel()) {
                return $this->indexAction();
            }
        }

        $this->viewModel->setTemplate('rss/reader/new');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model)
                return $this->newAction($model);
        }
        return $this->invalidRequest('admin/rss-reader');
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);

            if ($id && $field && has_value($value)) {
                if (in_array($field, array('readInterval'))) {
                    $this->getTable()->update(array($field => $value), array('id' => $id));
                    return new JsonModel(array('status' => 1));
                }
            }
        }

        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
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

    public function configAction()
    {
        /* @var $config Config */
        $config = getConfig('newsletter');
        $form = prepareConfigForm(new \RSS\Form\Config($tagId));
        $form->setData($config->varValue);


        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getConfigTable()->save($config);
                    db_log_info("Newsletter Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Newsletter Text configs saved successfully');
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    /**
     * @return ReaderTable
     */
    private function getTable()
    {
        return getSM('rss_reader_table');
    }
}
