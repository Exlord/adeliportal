<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace DigitalLibrary\Controller;

use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DigitalLibrary\Form\Book;
use DigitalLibrary\Model\BookTable;
use File\API\PrivateFile;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class Admin extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('book_table');
        $grid->route = 'admin/book';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('title', 'Title');

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $title, $edit, $del));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('digital-library/admin/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $hasFileUploadField = false;
        $flagEdit = false;
        $hasColorField = false;
        $fieldsId = 0;

        $form = new Book(($model != null));
        if (!$model) {
            $model = new \DigitalLibrary\Model\Book();
            $form->setAction(url('admin/book/new'));
        } else {
            $form->setAction(url('admin/book/edit', array('id' => $model->id)));
            $form = prepareForm($form, array('submit-new'));
            $flagEdit = true;
            $fieldsId = $model->fields['id'];
        }
        $form->bind($model);

        $hasFileUploadField = $this->getFieldsApi()->hasFileUploadField;
        $hasColorField = $this->getFieldsApi()->hasColorField;

        if ($this->request->isPost()) {

            if ($this->isSubmit()) {

                $post = array_merge_recursive(
                    $this->request->getPost()->toArray(),
                    $this->request->getFiles()->toArray()
                );

                $form->setData($post);
                if ($form->isValid()) {
                    $this->getTable()->save($model);

                    //fields
                    $fields = $model->getFields();
                    if ($flagEdit)
                        $fields['id'] = $fieldsId;
                    $this->getFieldsApi()->save('books', $model->id, $fields);

                    //category item
                    if (isset($model->category) && !empty($model->category)) {
                        if (!is_array($model->category))
                            $model->category = array($model->category);
                        getSM('entity_relation_table')->saveAll($model->id, 'books', $model->category);
                    }

                    //private files
                    if (!is_array($model->files))
                        $model->files = array($model->files);
                    PrivateFile::SetUsage($model->files, 'books', $model->id);

                    $this->flashMessenger()->addSuccessMessage('New Book Created Successfully');
                    if ($this->isSubmitAndClose())
                        return $this->indexAction();
                    elseif ($this->isSubmitAndNew()) {
                        $model = new \DigitalLibrary\Model\Book();
                        $form->bind($model);
                    } elseif (!$flagEdit)
                        return $this->indexAction();
                } else {
//                    debug($form->getMessages());
//                    debug($form->getInputFilter()->getMessages());
                    $this->formHasErrors();
                }

            } elseif ($this->isCancel()) {
                return $this->indexAction();
            }
        }

        $this->viewModel->setTemplate('digital-library/admin/new');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model) {
                $this->getFieldsApi()->init('books');
                $model->fields = $this->getFieldsApi()->getFieldData($model->id);
                $model->category = getSM('entity_relation_table')->getItemsIdArray($model->id, 'books');
                $model->files = PrivateFile::GetUsedFiles('books', $model->id);

                return $this->newAction($model);
            }
        }
        return $this->invalidRequest('admin/rss-reader');
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getTable()->remove($id);
                $this->getFieldsApi()->init('books');
                if (!is_array($id))
                    $id = array($id);
                foreach ($id as $itemId) {
                    $this->getFieldsApi()->remove($itemId);
                    getSM('entity_relation_table')->removeByEntityId($itemId, 'books');
                    getSM('private_file_usage')->removeByEntity('books', $itemId);
                }
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    /**
     * @return BookTable
     */
    private function getTable()
    {
        return getSM('book_table');
    }
}
