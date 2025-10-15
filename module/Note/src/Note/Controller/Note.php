<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Note\Controller;

use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use Note\Model\NoteTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class Note extends BaseAbstractActionController
{
//    public function indexAction()
//    {
//        $grid = new DataGrid('note_table');
//        $grid->route = 'admin/note';
//
//        $id = new Column('id', 'Id', array(
//            'headerAttr' => array('width' => '70px', 'align' => 'center'),
//            'attr' => array('align' => 'center')
//        ));
//        $grid->setIdCell($id);
//
//        $createDate = new Date('createDate', 'NOTE_CREATE_DATE');
//        $expireDate = new Date('expireDate', 'NOTE_EXPIRE_DATE');
//
//        $status = new Select('status', 'Status',
//            array('0' => t('Inactive'), '1' => t('Active')),
//            array('0' => 'inactive', '1' => 'active'),
//            array('headerAttr' => array('width' => '50px')));
//
//        $showDesktop = new Custom('showDesktop', 'NOTE_SHOW_DESKTOP', function (Column $col) {
//            $class = 'publish-up';
//            if ((int)$col->dataRow->showDesktop == 0)
//                $class = 'publish-down';
//            return '<div class="' . $class . '" ></div>';
//        }, array('headerAttr' => array('width' => '50px'), 'attr' => array('align' => 'center')));
//
//        $text = new Custom('text', 'NOTE', function (Column $col) {
//            return '<span data-tooltip="' . $col->dataRow->text . '" class="show-comment-icon" ></span>';
//        }, array('headerAttr' => array('width' => '34px'), 'attr' => array('align' => 'center')));
//
//        $del = new DeleteButton();
//        $edit = new EditButton();
//
//        $grid->addColumns(array($id, $createDate, $expireDate, $showDesktop, $status, $text, $edit, $del));
//        $grid->addDeleteSelectedButton();
//        $grid->addNewButton('NOTE_NEW');
//        $this->viewModel->setTemplate('note/admin/index');
//        $this->viewModel->setVariables(
//            array(
//                'grid' => $grid->render(),
//                'buttons' => $grid->getButtons(),
//            ));
//        return $this->viewModel;
//    }

    public function newAction($model = null)
    {
        $entityType = $this->params()->fromPost('entityType', false);
        getSM('note_api')->setVisibility($entityType);

        $form = new \Note\Form\Note();
        if (!$model) {
            $model = new \Note\Model\Note();
            $model->owner = current_user()->id;
            $model->date = time();
            $form->setAction(url('admin/note/new'));
        } else {
            $form->setAction(url('admin/note/edit', array('id' => $model->id)));
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {

                    $result = $this->getTable()->save($model);
                    if ($result || $result == 0) {
                        return new JsonModel(array('result' => 'success', 'id' => $model->id));
                    }
                    $this->flashMessenger()->addSuccessMessage('note saved successfully');
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setTemplate('note/new');
        $this->viewModel->setVariables(array('form' => $form));
        $this->viewModel->setTerminal(true);
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
        return $this->invalidRequest('admin/note');
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
     * @return NoteTable
     */
    private function getTable()
    {
        return getSM('note_table');
    }
}
