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
use Application\API\Breadcrumb;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use FormsManager\Form\DynamicForm;
use FormsManager\Form\FormTemplate;
use FormsManager\API;
use FormsManager\Model\Form;
use FormsManager\Model\FormData;
use FormsManager\Model\FormDataTable;
use FormsManager\Model\FormTable;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use System\DB\BaseTableGateway;
use Zend\Dom\Query;
use Zend\Form\Element;
use Zend\InputFilter\Factory;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class FormsDataController extends BaseAbstractActionController
{
    public function indexAction()
    {
        /* @var $dateFormat callable */
        $dateFormat = $this->vhm()->get('DateFormat');
        $formId = $this->params()->fromRoute('form-id', 0);
        $form = $this->getFormTable()->get($formId);
//        $table = $this->getFieldsApi()->init('form_'.$formId);
//        $table_gateway = new BaseTableGateway($table);
        $grid = new DataGrid('form_data_table');
        $grid->route = 'admin/forms-data';
        $grid->routeParams['form-id'] = $formId;

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

        $createdTime = new Custom('createdTime', 'Created Time', function (Column $col) use ($dateFormat) {
            return $dateFormat($col->dataRow->createdTime);
        });

        $userId = new Button('Creator', function (Button $col) {
                $col->route = 'admin/users/view';
                $col->routeParams['id'] = $col->dataRow->userId;
                $col->text = $col->dataRow->username;
            },
            array(
                'contentAttr' => array(
                    'class' => array('ajax_page_load')
                )
            ));

        $editBtn = new EditButton();
        $deleteBtn = new DeleteButton();
        $viewDataBtn = new Button('View', function (Button $col) {
            $col->route = 'admin/forms-data/view';
            $col->routeParams['form-id'] = $col->dataRow->formId;
            $col->routeParams['id'] = $col->dataRow->id;
            $col->contentAttr['class'][] = 'ajax_page_load';
            $col->icon = 'glyphicon glyphicon-eye-open text-primary';
        }, array(
            'headerAttr' => array(),
            'contentAttr' => array(
                'class' => array('ajax_page_load', 'btn', 'btn-default'),
                'title' => 'Admin Side View'
            ),
        ));
        $viewDataBtn2 = new Button('Client View', function (Button $col) use ($form) {
            $col->route = 'app/view-form-data';
            $col->routeParams['form-id'] = $col->dataRow->formId;
            $col->routeParams['id'] = $col->dataRow->id;
            $col->routeParams['form-title'] = App::prepareUrlString($form->title);
            $col->icon = 'glyphicon glyphicon-eye-open text-info';
        }, array(
            'headerAttr' => array(),
            'contentAttr' => array(
                'target' => '_blank',
                'class' => array('btn', 'btn-default'),
                'title' => 'Client side view'
            ),
        ));

        $grid->addNewButton('New Form Data');
        $grid->addDeleteSelectedButton();

        $grid->addColumns(array($id, $createdTime, $userId, $viewDataBtn, $viewDataBtn2, $editBtn, $deleteBtn));

        $grid->getSelect()
            ->join(array('u' => 'tbl_users'), $grid->getTableGateway()->table . '.userId=u.id', array('username'), 'LEFT')
            ->where(array($grid->getTableGateway()->table . '.formId' => $formId));

        $this->viewModel->setTemplate('forms-manager/forms-data/index');
        $this->viewModel->setVariables(array('grid' => $grid->render(), 'buttons' => $grid->getButtons(), 'form' => $form));
        return $this->viewModel;
    }

    public function newAction($formDataId = false)
    {
        $type = $this->params()->fromRoute('type', 'admin');

        $editing = $formDataId;
        $formId = $this->params()->fromRoute('form-id', 0);
        if (!$formId) {
            if ($type == 'admin')
                return $this->invalidRequest('admin/forms');
            else
                return $this->invalidRequest('app/front-page');
        }

        $formModel = $this->getFormTable()->getById($formId);
        if (!$formModel) {
            if ($type == 'admin')
                return $this->invalidRequest('admin/forms');
            else
                return $this->invalidRequest('app/front-page');
        }

        if ($type == 'app') {
            Breadcrumb::AddMvcPage($formModel->title, 'app/new-forms-data',
                array('form-id' => $formModel->id, 'form-title' => App::prepareUrlString($formModel->title)));
        }

        if (!empty($formModel->config))
            $formModel->config = unserialize($formModel->config);

        $captcha = (isset($formModel->config['use_captcha']) && $formModel->config['use_captcha'] == '1');
        $formType = $formModel->formType;
        $form = new FormTemplate($captcha, $formModel->id);

        if ($type == 'admin')
            $form->setAction(url('admin/forms-data/new', array('form-id' => $formId)));
        else
            $form->setAction(url('app/new-forms-data',
                array('form-id' => $formId, 'form-title' => App::prepareUrlString($formModel->title))));

        if ($editing)
            $form->setAction(url('admin/forms-data/edit', array('form-id' => $formId, 'id' => $formDataId)));
        /* @var $form FormTemplate */
        $form = prepareConfigForm($form);

        $html = null;
        $fields = null;

        $hasFileUploadField = false;
        $hasColorField = false;
        if ($formType == API\Form::CUSTOM_FORM) {
            $fieldIds = $this->extractFieldsFromTemplate($formModel);
            if (count($fieldIds)) {
                $inputFilters = $this->getFieldsApi()->loadFieldsById($fieldIds, $form);
                //input filters should be provided as on array in order for the Collection to work
                $form->setInputFiltersConfig($inputFilters);
                $hasFileUploadField = $this->getFieldsApi()->hasFileUploadField;
                $hasColorField = $this->getFieldsApi()->hasColorField;
            }
        }

//            $test2 = $form->getInputFilter()->get('test2');
//            $test2->setRequired(true);
//            $test2->setAllowEmpty(false);

        $data = array();
        if ($editing) {
            $data = $this->getFormDataTable()->getData($formDataId, $formId, $fieldIds, false, $formModel->editable);
            $form->setData($data);
        }

        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if ($hasFileUploadField)
                $post = array_merge_recursive($post, $this->request->getFiles()->toArray());

            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    $post = (array)$form->getData();
                    $model = new FormData();
                    $model->createdTime = isset($data['createdTime']) ? $data['createdTime'] : time();
                    $model->formId = $formId;
                    $model->userId = current_user()->id;

                    if ($editing) {
                        if ($formModel->editable) {
                            $model->editTime = time();
                            $model->id = $data['formDataId'];
                        } else
                            $model->createdTime = time();
                    }

                    $___formDataId = $this->getFormDataTable()->save($model);
                    if (!$editing || ($editing && !$formModel->editable))
                        $formDataId = $___formDataId;

                    $post_data = $post;
                    unset($post_data['buttons']);
                    if ($editing && $formModel->editable)
                        $post_data['id'] = $data['id'];

                    $this->getFieldsApi()->save('form_' . $formId, $formDataId, $post_data, $fieldIds);

                    $message = 'New Form data Created Successfully.';
                    if (isset($formModel->config['after_save_message']) && !empty($formModel->config['after_save_message']))
                        $message = $formModel->config['after_save_message'];
                    $this->flashMessenger()->addSuccessMessage($message);

                    if (!empty($formModel->email)) {
                        $mail_html = $this->forward()->dispatch('FormsManager\Controller\FormsData', array(
                            'action' => 'view',
                            'form-id' => $formModel->id,
                            'id' => $formDataId,
                            'viewMode' => 'print'
                        ));
                        $mail_html = $this->render($mail_html);
                        send_mail($formModel->email,
                            Mail::getFrom('forms_config'),
                            sprintf(t('New data submitted for %s.'), $formModel->title),
                            $mail_html, 'FormsManager', 0);
                    }
                    if ($type == 'admin')
                        return $this->redirect()->toRoute('admin/forms-data/view',
                            array('form-id' => $formId, 'id' => $formDataId));


                    $form->setData(array());
                }
            }
        }
        if ($formType == API\Form::CUSTOM_FORM) {
            $html = $this->renderCustomForm($formModel->format, $form, $formId);
        }
        $this->viewModel->setTerminal(false);
        $this->viewModel->setTemplate('forms-manager/forms-data/new');
        $this->viewModel->setVariables(array(
            'formModel' => $formModel,
            'form' => $form,
            'html' => $html,
            'captcha' => $captcha,
            'hasColorField' => $hasColorField
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $formId = $this->params()->fromRoute('form-id', 0);
        if (!$formId)
            return $this->invalidRequest('admin/forms');

        $formModel = $this->getFormTable()->get($formId);
        if (!$formModel->editable)
            $this->flashMessenger()->addInfoMessage('Notice : this form data is set to read only and the submitted data will be saved with a new ID.');
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('admin/forms');

        return $this->newAction($id);
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $formDataId = $this->params()->fromPost('id', 0);
            if ($formDataId) {
                if (!is_array($formDataId))
                    $formDataId = array($formDataId);
                foreach ($formDataId as $id) {
                    $formData = $this->getFormDataTable()->get($id);
                    $this->getFormDataTable()->remove($id);
                    $this->getFieldsApi()->init('form_' . $formData->formId);
                    $this->getFieldsApi()->remove($formData->formId);
                }
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function viewAction()
    {
        $isAdminSide = strpos($this->request->getUriString(), 'admin') > -1;
        $print = $this->params()->fromRoute('viewMode', 'view') == 'print';
        if ($print)
            $this->viewModel->setTerminal(true);

        $formId = $this->params()->fromRoute('form-id', 0);
        if (!$formId)
            return $this->invalidRequest('admin/forms');

        $dataId = $this->params()->fromRoute('id', 0);
        if (!$dataId) {
            if ($isAdminSide)
                return $this->invalidRequest('admin/forms-data', array('form-id' => $formId));
            else
                return $this->invalidRequest('app/front-page');
        }

        $form = $this->getFormTable()->getById($formId);
        $fieldIds = $this->extractFieldsFromTemplate($form);
        $data = $this->getFormDataTable()->getData($dataId, $formId, $fieldIds, true);
        $fieldNames = $this->getFieldsNames($form, $fieldIds);

        $this->viewModel->setVariables(array('form' => $form, 'data' => $data, 'fields' => $fieldNames, 'print' => $print));
        $this->viewModel->setTemplate('forms-manager/forms-data/view');
        return $this->viewModel;
    }

    //region private methods
    /**
     * @return FormTable
     */
    private function getFormTable()
    {
        return getSM('forms_table');
    }

    /**
     * @return FormDataTable
     */
    private function getFormDataTable()
    {
        return getSM('form_data_table');
    }

    private function extractFieldsFromTemplate($form)
    {
        $cacheKey = 'form_fields_list_' . $form->id;
        if (!$fields = getCacheItem($cacheKey)) {
            $fields = array();
            $regex = '/__FIELD__\d+__/';
            $matches = array();
            preg_match_all($regex, $form->format, $matches);
            foreach ($matches[0] as $field) {
                preg_match('/\d+/', $field, $id);
                $fields[] = $id[0];
            }
            setCacheItem($cacheKey, $fields);
        }
        return $fields;
    }

    private function renderCustomForm($format, \Zend\Form\Form $form, $formId)
    {
        $cacheKey = 'forms_data_rendered_template_' . $formId;
//        if (cacheExist($cacheKey) && !$this->request->isPost())
//            $html = getCacheItem($cacheKey);
//        else {
        /* @var $formElement callable */
        $formElement = $this->vhm()->get('formElement');
        /* @var $formCollection callable */
        $formCollection = $this->vhm()->get('formCollection');
        /* @var $formError callable */
        $formError = $this->vhm()->get('formElementErrors');
        $formError->setAttributes(array('class' => 'form-element-error'));
        $form->setOption('twb-layout', 'inline');

        $form->prepare();
        $search = array();
        $replace = array();
        $errors = array();
        /* @var $el Element */
        foreach ($form->getElements() as $el) {
            if ($el->getName() != 'captcha') {
                $id = $el->fieldId;
                $search[] = '__FIELD__' . $id . '__';
                $replace[] = $formElement($el);
                $error = $formError($el);
                if ($error)
                    $errors[] = $el->getLabel() . $error;
            }
        }
        foreach ($form->getFieldsets() as $el) {
            if (!$el instanceof \System\Form\Buttons) {
                $id = $el->fieldId;
                $search[] = '__FIELD__' . $id . '__';
                $replace[] = $formCollection($el);
                $error = $formError($el);
                if ($error)
                    $errors[] = $el->getLabel() . $error;
            }
        }
        $html = str_replace($search, $replace, $format);
        $error = $this->renderFormErrors($errors);
        $html = $error . $html . $error;
//        setCacheItem($cacheKey, $html);
//        }
        return $html;
    }

    private function getFieldsNames($form, $fieldIds)
    {
        $cacheKey = 'form_field_names_' . $form->id;
        if (!$fieldNames = getCacheItem($cacheKey)) {
            $data = $this->getFieldsTable()->get($fieldIds);
            if (count($fieldIds) == 1)
                $data = array($data);

            $fieldNames = array();
            foreach ($data as $row) {
                $fieldNames[$row->id] = (array)$row;
            }
            setCacheItem($cacheKey, $fieldNames);
        }
        return $fieldNames;
    }

    private function renderFormErrors($errors)
    {
        $errorText = '<ul>%s</ul>';
        $errorItem = "<li>%s</li>";
        $html = '';
        if (is_array($errors) && count($errors)) {
            foreach ($errors as $error) {
                $html .= sprintf($errorItem, $error);
            }
            $html = sprintf($errorText, $html);
        }
        return $html;
    }
    //endregion
}
