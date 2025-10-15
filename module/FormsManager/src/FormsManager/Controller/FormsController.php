<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace FormsManager\Controller;

use Application\API\App;
use Application\Model\Config;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use FormsManager\Form\DynamicForm;
use FormsManager\Model\Form;
use FormsManager\Model\FormTable;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;
use DataView\API\Grid;
use DataView\API\GridColumn;

class FormsController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('forms_table');
        $grid->route = 'admin/forms';

        $id = new Column('id', 'Id',
            array(
                'headerAttr' => array(
                    'width' => '50px',
                    'align' => 'center'
                ),
                'attr' => array(
                    'align' => 'center'
                )
            )
        );
        $grid->setIdCell($id);

        $title = new Column('title', 'Title');

        $editBtn = new EditButton();
        $deleteBtn = new DeleteButton();
        $viewDataBtn = new Button('Data List', function (Button $col) {
            $col->route = 'admin/forms-data';
            $col->routeParams['form-id'] = $col->dataRow->id;
            $col->icon = 'glyphicon glyphicon-list-alt text-info';
        }, array(
            'headerAttr' => array(
                'title' => t('Here tou can View/Edit or Delete the data, filled up for this form.')
            ),
            'contentAttr' => array(
                'class' => array('ajax_page_load', 'btn', 'btn-default')
            ),
        ));

        $newDataBtn = new Button('New Data', function (Button $col) {
            $col->route = 'app/new-forms-data';
            $col->routeParams['form-id'] = $col->dataRow->id;
            $col->routeParams['form-title'] = App::prepareUrlString($col->dataRow->title);
            $col->contentAttr['target'] = '_blank';
            $col->contentAttr['data-icon-s'] = 'ui-icon-plus';
            $col->icon = 'glyphicon glyphicon-plus text-success';
        }, array(
            'headerAttr' => array(
                'title' => t('Client side data entry form')
            ),
            'contentAttr' => array(
                'class' => array('btn', 'btn-default')
            ),
        ));

        $grid->addNewButton('New Form');
        $grid->addDeleteSelectedButton();

        $grid->addColumns(array($id, $title, $newDataBtn, $viewDataBtn, $editBtn, $deleteBtn));

        $this->viewModel->setTemplate('forms-manager/forms/index');
        $this->viewModel->setVariables(array('grid' => $grid->render(), 'buttons' => $grid->getButtons()));
        return $this->viewModel;
    }

    /**
     * @param null|Form $model
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction($model = null)
    {
        $this->getFields();
        $form = new DynamicForm();
        $form->setAction('admin/forms/new');

        if (!$model) {
            $model = new Form();
            $form->setAction(url('admin/forms/new'));
        } else
            $form->setAction(url('admin/forms/edit', array('id' => $model->id)));

        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {

                    $model->config = serialize($model->config);
                    $this->getTable()->save($model);
                    $this->flashMessenger()->addSuccessMessage('New Form Created Successfully.');
                    if (!isset($post['buttons']['submit-new']))
                        return $this->indexAction();
                    else {
                        $model = new Form();
                        $form->bind($model);
                    }
                }
            }
        }

        $this->viewModel->setTemplate('forms-manager/forms/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('admin/forms');

        $model = $this->getTable()->get($id);
        if (!$model)
            return $this->invalidRequest('admin/forms');
        if (!empty($model->config))
            $model->config = unserialize($model->config);
        return $this->newAction($model);
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                //TODO delete data for this form
                $this->getTable()->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('forms_config');
        $form = new \FormsManager\Form\Config();
        $form->setAction(url('admin/configs/forms'))->setData(Mail::getDefaultSender());
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
                    db_log_info("Forms Configs changed");
                    $this->flashMessenger()->addInfoMessage('Forms configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('forms-manager/forms/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    /**
     * @return FormTable
     */
    private function getTable()
    {
        return getSM('forms_table');
    }

    private function getFields()
    {
        $fields = $this->getFieldsTable()->getActiveFields('form_manager');
        $this->viewModel->setTemplate('forms-manager/forms/fields');
        $this->viewModel->setVariables(array('fields' => $fields, 'types' => $this->getFieldsTable()->fieldTypes));
        $this->viewModel->setVariable('fields', $this->render($this->viewModel));
    }
}
