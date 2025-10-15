<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ProductShowcase\Controller;

use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use ProductShowcase\Model\PsTable;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class PsAdminController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('product_showcase_table');
        $grid->route = 'admin/product-showcase';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('title', 'Title');
        $date = new Date('date', 'Date');

        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $title, $date, $status, $edit, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('PS_NEW_PRODUCT_SHOWCASE');

        $this->viewModel->setTemplate('product-showcase/product-showcase-admin/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function newAction($model = null, $imagesFile = array())
    {
        $hasFileUploadField = false;
        $oldImage = '';
        $flagEdit = false;
        $hasColorField = false;
        $fieldsId = 0;
        $selectCat = getSM('category_table')->getAll(array('catMachineName' => \ProductShowcase\Module::PS_ENTITY_TYPE))->current();
        if (isset($selectCat->id)) {
            $catArray = getSM('category_item_table')->getItemsTreeByMachineName(\ProductShowcase\Module::PS_ENTITY_TYPE);
            $form = new \ProductShowcase\Form\ProductShowcase($catArray);
            if (!$model) {
                $model = new \ProductShowcase\Model\Ps();
                $form->setAction(url('admin/product-showcase/new'));
            } else {
                $form->setAction(url('admin/product-showcase/edit', array('id' => $model->id)));
                $form = prepareForm($form, array('submit-new'));
                $flagEdit = true;
                $fieldsId = $model->fields['id'];
            }

            $hasFileUploadField = $this->getFieldsApi()->hasFileUploadField;
            $hasColorField = $this->getFieldsApi()->hasColorField;
            $form->bind($model);

            if ($this->request->isPost()) {
                $imageValue = $this->request->getFiles()->toArray();
                $post = $this->request->getPost()->toArray();
                if ($hasFileUploadField)
                    $post = array_merge_recursive($post, $this->request->getFiles()->toArray());
                /*$file = $this->request->getFiles()->toArray();
                $post = $this->request->getPost()->toArray();*/
                if ($this->isSubmit()) {
                    $form->setData($post);

                    if ($form->isValid()) {

                        $fields = $model->getFields();
                        if ($flagEdit) {
                            $fields['id'] = $fieldsId;
                        }
                        $model->date = time();

                        $id = $this->getTable()->save($model);
                        $this->getFieldsApi()->init(\ProductShowcase\Module::PS_ENTITY_TYPE);
                        $this->getFieldsApi()->save(\ProductShowcase\Module::PS_ENTITY_TYPE, $model->id, $fields);
                        if ($model->catId) {
                            if (!is_array($model->catId))
                                $model->catId = array($model->catId);
                            getSM('entity_relation_table')->saveAll($model->id, \ProductShowcase\Module::PS_ENTITY_TYPE, $model->catId);
                        }
                        //upload image
                        if (isset($post['image']['image']) && isset($imageValue['image']['image'])) {
                            $imageOptions = $post['image']['image'];
                            $imageArray = $imageValue['image']['image'];

                            if ($imageArray) {
                                $files = array();
                                foreach ($imageArray as $key => $row) {
                                    if (isset($row['imageValue']['name']) && isset($row['imageValue']['tmp_name']) && !empty($row['imageValue']['tmp_name'])) {
                                        if (isset($imageOptions[$key]['imageTitle']))
                                            $files[$key]['title'] = $imageOptions[$key]['imageTitle'];
                                        if (isset($imageOptions[$key]['imageAlt']))
                                            $files[$key]['alt'] = $imageOptions[$key]['imageAlt'];
                                        $files[$key]['tmp_name'] = $row['imageValue']['tmp_name'];
                                        $files[$key]['name'] = $row['imageValue']['name'];
                                        $files[$key]['fileType'] = $row['imageValue']['type'];
                                    }
                                }
                                $this->getFileApi()->save(\ProductShowcase\Module::PS_ENTITY_TYPE, $model->id, $files, 100);
                            }
                        }
                        //end
                        $this->flashMessenger()->addSuccessMessage('PS_NEW_SUCCESS');
                        if ($this->isSubmitAndClose())
                            return $this->indexAction();
                        elseif ($this->isSubmitAndNew()) {
                            $model = new \ProductShowcase\Model\Ps();
                            $form->bind($model);
                        }
                    } else {
                        $this->formHasErrors();
                    }

                } elseif ($this->isCancel()) {
                    return $this->indexAction();
                }
            }

            $this->viewModel->setTemplate('product-showcase/product-showcase-admin/new');
            $this->viewModel->setVariables(array('form' => $form, 'imagesFile' => $imagesFile, 'hasColorField' => $hasColorField));
            return $this->viewModel;
        } else {
            $this->flashMessenger()->addErrorMessage(sprintf(t('PS_ALERT_CREATE_CATEGORY'), \ProductShowcase\Module::PS_ENTITY_TYPE));
            return $this->indexAction();
        }
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model) {
                $imagesFile = getSM('file_table')->getByEntityType(\ProductShowcase\Module::PS_ENTITY_TYPE, $id, true);
                $model->catId = getSM('entity_relation_table')->getItemsIdArray($id, \ProductShowcase\Module::PS_ENTITY_TYPE);
                $this->getFieldsApi()->init(\ProductShowcase\Module::PS_ENTITY_TYPE);
                $model->fields = $this->getFieldsApi()->getFieldData($id);
                return $this->newAction($model, $imagesFile);
            }
        }
        return $this->invalidRequest('admin/product-showcase');
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);

            if ($id && $field && has_value($value)) {
                $this->getTable()->update(array($field => $value), array('id' => $id));
                return new JsonModel(array('status' => 1));
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
                $this->getFieldsApi()->init(\ProductShowcase\Module::PS_ENTITY_TYPE);
                $this->getFieldsApi()->remove($id);
                getSM('entity_relation_table')->removeByEntityId($id, 'product_showcase');
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function deleteImgAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost();
            if ($id) {
                getSM('page_table')->update(array('image' => null), array('id' => $id));
                return new JsonModel(array(
                    'status' => 1,
                ));
            }
        }
        return new JsonModel(array(
            'status' => 0,
        ));
    }

//    public function configAction()
//    {
//        /* @var $config Config */
//        $config = getConfig('newsletter');
//        $form = prepareConfigForm(new \RSS\Form\Config($tagId));
//        $form->setData($config->varValue);
//
//
//        if ($this->request->isPost()) {
//            $post = $this->request->getPost();
//            if (isset($post['buttons']['submit'])) {
//                $form->setData($this->request->getPost());
//                if ($form->isValid()) {
//                    $config->setVarValue($form->getData());
//                    $this->getConfigTable()->save($config);
//                    db_log_info("Newsletter Configs changed");
//                    $this->flashMessenger()->addSuccessMessage('Newsletter Text configs saved successfully');
//                }
//            }
//        }
//
//        $this->viewModel->setVariables(array('form' => $form));
//        return $this->viewModel;
//    }

    /**
     * @return PsTable
     */
    private function getTable()
    {
        return getSM('product_showcase_table');
    }
}
