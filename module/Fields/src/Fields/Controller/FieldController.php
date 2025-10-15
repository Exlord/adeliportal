<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Fields\Controller;

use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use DataView\Lib\Visualizer;
use Fields\Form;
use Fields\Model;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;


class FieldController extends BaseAbstractActionController
{
    public $entityTypes = null;

    private function _getEntityTypes()
    {
        if (!$this->entityTypes) {
            $config = getSM('Config');
            $this->entityTypes = $config['fields_entities'];
            $this->getEventManager()->trigger('Fields.EntityTypes.Load', $this);
        }
        return $this->entityTypes;
    }

    public function entityTypesAction()
    {
        $this->viewModel->setVariables(array(
            'entities' => $this->_getEntityTypes()
        ));
        $this->viewModel->setTemplate('fields/field/entity-types');
        return $this->viewModel;
    }

    public function indexAction($entityType = null)
    {
        if (!$entityType)
            $entityType = $this->params()->fromRoute('entityType', false);
        if (!$entityType)
            return $this->entityTypesAction();

        $grid = new DataGrid('fields_table');
        $grid->route = 'admin/fields';
        $grid->routeParams['entityType'] = $entityType;
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $fieldName = new Column('fieldName', 'Name');
        $fieldMachineName = new Column('fieldMachineName', 'Machine Name');
        $fieldType = new Column('fieldType', 'Type');
        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $status2 = new Custom('collection', '', function (Custom $col) {
            $content = '';
            if ($col->dataRow->collection == '1') {
                $title = t("This field belongs to a collection");
                $content .= "<span class='glyphicon glyphicon-th-list text-primary grid-icon' title='{$title}'></span>";
            }

            $validators = unserialize($col->dataRow->validators);
            if(isset($validators['required']) && $validators['required'] == '1'){
                $title = t("This fields value is required");
                $content .= "<span class='glyphicon glyphicon-asterisk text-danger grid-icon' title='{$title}'></span>";
            }
            if(isset($validators['allow_empty']) && $validators['allow_empty'] == '1'){
                $title = t("This fields value cannot be empty");
                $content .= "<span class='glyphicon glyphicon-remove-circle text-danger grid-icon' title='{$title}'></span>";
            }


            return $content;
        });

        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, $fieldName, $fieldMachineName, $fieldType, $status2, $status, $edit, $delete));
        $grid->addNewButton('New Field');
        $grid->addDeleteSelectedButton();

        if ($entityType != 'all') {
            $grid->getSelect()->where(array(
                'entityType' => $entityType
            ));
        }

        $this->viewModel->setTemplate('fields/field/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $entityTypes = $this->_getEntityTypes();
        $entityType = $this->params()->fromRoute('entityType', 'all');
        $form = new Form\Field($entityTypes, $entityType);
//        /* @var $collection \Zend\Form\Element\Collection */
//        $collection =  $form->get('fieldConfigData')->get('select_field')->get('keyValuePairs');
//        $newField = array(
//            'type' => 'Zend\Form\Element\Text',
//            'name' => 'field_price',
//            'attributes' => array(
//                'value' => 'PRICE'
//            )
//        );
//        $targetElement = $collection->getTargetElement();
//        $targetElement->add($newField);
//        foreach($collection->getFieldsets() as $el){
//            $el->add($newField);
//        }
////        $collection->setTargetElement($targetElement);

        if (!$model) {
            $model = new Model\Field();
            $form->setAttribute('action', url('admin/fields/new', array('entityType' => $entityType)));
        } else {
            $form->setAttribute('action', url('admin/fields/edit', array('id' => $model->id, 'entityType' => $entityType)));
            $form->get('buttons')->remove('submit-new');
        }
        $form->setAttribute('data-cancel', url('admin/fields', array('entityType' => $model->entityType)));
        $model->entityType = $entityType;

        $form->bind($model);
//        $fieldTypes = $this
//            ->getServiceLocator()
//            ->get('fields_table')
//            ->fieldTypes;

//        $form->get('fieldType')->setValueOptions($fieldTypes);


        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {

                $form->setData($post);

                if ($form->isValid()) {
                    $configs = $model->getFieldConfigData();
                    $conf_name = $model->fieldType . '_field';
                    $config = isset($configs[$conf_name]) ? $configs[$conf_name] : array();
                    $model->fieldConfigData = serialize(array($conf_name => $config));
                    //-------------------------------------------- Filters
                    $model->filters = $this->clearFilters($model->filters);
                    //-------------------------------------------- Validators
                    $model->validators = $this->clearValidators($model->validators);

                    $id = getSM()->get('fields_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
//                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction($model->entityType);
                    } else {
                        $newModel = new Model\Field();
                        $newModel->entityType = $model->entityType;
                        $form->setData(array());
//                        $form = new Form\Field($entityTypes);
//                        $form->get('fieldType')->setValueOptions($fieldTypes);
                        $form->bind($newModel);
                    }
                } else {
                    $this->formHasErrors();
                }
            }
        }

        $this->viewModel->setTemplate('fields/field/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    private function clearFilters($filters)
    {
        $__filters = $filters;
        foreach ($filters as $key => $value) {
            if (!is_array($value) || (is_array($value) && $value['apply'] !== '1'))
                unset($__filters[$key]);
        }
        return serialize($__filters);
    }

    private function clearValidators($validators)
    {
        $__validators = $validators;
        foreach ($validators as $key => $value) {
            $remove = false;
            if (is_array($value) && $value['apply'] !== '1') {
                $remove = true;
            }
            if (is_scalar($value) && !in_array($key, array('required', 'allow_empty'))) {
                $remove = true;
            } elseif (is_scalar($value)) {
                $__validators[$key] = (boolean)$value;
            }
            if ($remove)
                unset($__validators[$key]);
        }
        return serialize($__validators);
    }

    public function editAction()
    {
        ini_set('memory_limit', '1024M');
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('fields_table')->get($id);

        $model->fieldConfigData = unserialize($model->fieldConfigData);
        $model->filters = unserialize($model->filters);
        $model->validators = unserialize($model->validators);

        return $this->newAction($model);
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field && has_value($value)) {
                if ($field == 'status') {
                    $this->getServiceLocator()->get('fields_table')->update(array($field => $value), array('id' => $id));
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
                $this->getServiceLocator()->get('fields_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

}
