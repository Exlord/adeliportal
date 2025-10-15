<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/8/2014
 * Time: 9:45 AM
 */

namespace File\Controller;


use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use File\Model\PFile;
use File\Model\PFileTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class PrivateFile extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('private_file_table');
        $grid->route = 'admin/file/private';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $name = new Column('name', 'Name');
        $title = new Column('title', 'Title');

        $dl = new Button('Download', function (Button $col) {
            $col->route = 'app/pf-ddl';
            $col->routeParams['file'] = $col->dataRow->id;
            $col->contentAttr['class'][] = 'btn btn-default';
            $col->icon = "glyphicon glyphicon-download-alt text-primary";
        });

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $name, $title, $dl, $edit, $del));
        $grid->addNewButton('New');
        $grid->addDeleteSelectedButton();

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));

        $this->viewModel->setTemplate('file/private/index');
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $validators = \File\API\PrivateFile::getUploadValidators();
        $max_upload_size = $validators['max_upload_size'];
        ini_set('memory_limit', '128M');
        ini_set('upload_max_filesize', $max_upload_size);


        $oldModel = (array)$model;
        $isEdit = ($model != null);
        $form = new \File\Form\PrivateFile($isEdit);
        if (!$model) {
            $model = new PFile();
            $form->setAction(url('admin/file/private/new'));
            $form = prepareForm($form, array('submit-copy', 'submit-close'));
        } else {
            $form->setAction(url('admin/file/private/edit', array('id' => $model->id)));
            $form = prepareForm($form, array('submit-new'));
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            $files = $this->request->getFiles()->toArray();
            $post = array_merge($post, $files);
            if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {

                    if (isset($model->path) && isset($model->path['tmp_name']) && !empty($model->path['tmp_name'])) {
                        $model->path = \File\API\PrivateFile::MoveUploadedFile($model->path, '/pf');
                        if ($isEdit)
                            @unlink(PRIVATE_FILE . $oldModel['path']);
                    } else
                        $model->path = $oldModel['path'];

                    $oldModel = (array)$model;
                    $this->getTable()->save($model);

                    $this->flashMessenger()->addSuccessMessage('Private File saved.');
                    if ($this->isSubmitAndClose())
                        return $this->indexAction();
                    elseif ($this->isSubmitAndNew()) {
                        $model = new PFile();
                        $form->bind($model);
                    } elseif (!$isEdit)
                        return $this->indexAction();
                } else
                    $this->formHasErrors();

            } elseif ($this->isCancel()) {
                return $this->indexAction();
            }
        }

        $this->viewModel->setTemplate('file/private/new');
        $this->viewModel->setVariable('form', $form);
        if ($isEdit)
            $this->viewModel->setVariable('model', $oldModel);
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model) {
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
                $model = $this->getTable()->get($id);
                $this->getTable()->remove($id);
                @unlink(PRIVATE_FILE . $model->path);
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function directDownloadAction()
    {
        $file = $this->params()->fromRoute('file', false);
        if (!$file)
            return $this->invalidRequest('app/front-page');

        $model = $this->getTable()->get($file);
        if (!$model)
            return $this->invalidRequest('app/front-page');

        $roles = array();
        foreach (current_user()->roles as $r)
            $roles[] = $r['id'];

        if (count(array_intersect($roles, $model->accessibility)) > 0)
            return $this->plugin('stream')->binaryFile(PRIVATE_FILE . $model->path, $model->downloadAs . '.' . getFileExt($model->path));
        else {
            $this->flashMessenger()->addErrorMessage('You are not authorized to download this file.');
            return $this->redirect()->toRoute('app/front-page');
        }
    }

    public function readAction()
    {
        $file = $this->params()->fromRoute('file', false);
        if (!$file)
            return $this->invalidRequest('app/front-page');

        $model = $this->getTable()->get($file);
        if (!$model)
            return $this->invalidRequest('app/front-page');

        $roles = array();
        foreach (current_user()->roles as $r)
            $roles[] = $r['id'];

        if (count(array_intersect($roles, $model->accessibility)) > 0) {
            $response = $this->getResponse();
            $response->setContent(file_get_contents(PRIVATE_FILE . $model->path));
            return $response;
        } else {
            $this->flashMessenger()->addErrorMessage('You are not authorized to download this file.');
            return $this->redirect()->toRoute('app/front-page');
        }
    }

    /**
     * @return PFileTable
     */
    private function getTable()
    {
        return getSM('private_file_table');
    }
}