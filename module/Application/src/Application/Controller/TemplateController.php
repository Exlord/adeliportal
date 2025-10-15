<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\Config;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Visualizer;
use Mail\Form\Template;
use Mail\Model\MailArchiveTable;
use Mail\Model\MailTable;
use Mail\Model\TemplateTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class TemplateController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('template_table');
        $grid->route = 'admin/template';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('title', 'Title');

//        $showTemplate = new Custom('format', 'Show', function (Column $col) {
//            return "<span data-tooltip='" . $col->dataRow->format . "' class='btn btn-default'><i></i></span>";
//        }, array('headerAttr' => array('width' => '34px'), 'attr' => array('align' => 'center')));

        $showTemplate = new Button('Preview', function ($col) {
            $col->route = '#view-template-preview';
            $col->text = '';
            $col->icon = 'glyphicon glyphicon-eye-open';
            $col->contentAttr['data-tooltip'] = $col->dataRow->format;
            $col->contentAttr['class'][] = 'btn-default';
        }, array('headerAttr' => array('width' => '34px'), 'attr' => array('align' => 'center')));

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $title, $showTemplate, $edit, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('New Template');

        $this->viewModel->setTemplate('application/template/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    /**
     * @param \Application\Model\Template $model
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction($model = null)
    {
        $form = new \Application\Form\Template();
        if (!$model) {
            $model = new \Application\Model\Template();
            $form->setAction(url('admin/template/new'));
        } else {
            $form->setAction(url('admin/template/edit', array('id' => $model->id)));
            $form = prepareForm($form, array('submit-new'));
        }

        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    $this->getTable()->save($model);
                    $this->flashMessenger()->addSuccessMessage('New Email template Created Successfully.');
                    return $this->indexAction();
                } else
                    $this->formHasErrors();
            } elseif (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit-new'])) {
                $model = new \Application\Model\Template();
                $form->bind($model);
            }
        }

        $placeholders = getSM('Config');
        $placeholders = $placeholders['template_placeholders'];

        $this->viewModel->setTemplate('application/template/new');
        $this->viewModel->setVariables(array('form' => $form, 'placeholders' => $placeholders));
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
        return $this->invalidRequest('admin/template');
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

    /**
     * @return \Application\Model\TemplateTable
     */
    private function getTable()
    {
        return getSM('template_table');
    }
}
