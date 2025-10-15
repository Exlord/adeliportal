<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;


use Application\API\Backup\Db;
use Application\Form;
use Application\Model;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use System\Controller\BaseAbstractActionController;
use System\IO\File;
use Zend\View\Model\JsonModel;

class DbBackupController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $api = $this->getApi();
        $grid = new DataGrid('db_backup_table');
        $grid->route = 'admin/backup/db';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $file = new Column('file', 'Archive File');
        $comment = new Column('comment', "Creator's Comment");
        $created = new Custom('created', 'Create Date', function (Custom $col) {
            /* @var $date callable */
            $date = getSM('viewhelpermanager')->get('DateFormat');
            return $date($col->dataRow->created, 0, 0);
        }, array('attr' => array('align' => 'center')));

        $restored = new Custom('restored', 'Last Restore Date', function (Custom $col) {
            $value = $col->dataRow->restored;
            if ($value) {
                /* @var $date callable */
                $date = getSM('viewhelpermanager')->get('DateFormat');
                return $date($value, 0, 0);
            }
            return t('Never');
        }, array('attr' => array('align' => 'center')));

        $username = new Custom('username', 'Creator', function (Custom $col) {
            $id = $col->dataRow->userId;
            if ($id) {
                return sprintf("<a href='%s' class='ajax_page_load'>%s</a>", url('admin/users/view', array('id' => $id)), $col->dataRow->username);
            }
            return t('System');
        }, array('attr' => array('align' => 'center')));

        $size = new Custom('size', 'File Size', function (Custom $col) {
            $size = $col->dataRow->size;
            $size = File::FormatFileSize($size);
            return "<span class='digit' dir='ltr'>" . $size . '</span>';
        }, array('attr' => array('align' => 'center')));

        $restore = new Button('Restore', function (Button $col) {
            $col->route = 'admin/backup/db/restore';
            $col->routeParams['id'] = $col->dataRow->id;
            $col->icon = 'glyphicon glyphicon-refresh';
        }, array('contentAttr' => array('class' => array('btn btn-default', 'ajax_page_load'))));

        $download = new Button('Download', function (Button $col) {
            $col->route = 'admin/backup/db/download';
            $col->routeParams['file'] = $col->dataRow->file;
            $col->icon = 'glyphicon glyphicon-download-alt';
        }, array('contentAttr' => array('class' => array('btn btn-default'), 'target' => '_blank')));

        $del = new DeleteButton();

        $grid->addColumns(array($id, $file, $comment, $created, $restored, $size, $username, $restore, $download, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('New Backup');
        $grid->defaultSort = $id;
        $grid->defaultSortDirection = $grid::SORT_DESC;
        $grid->getSelect()
            ->join(array('u' => 'tbl_users'), $grid->getTableGateway()->table . '.userId=u.id', array('username'), 'LEFT');

        if ($api->isLocked())
            $grid->addButton('Manually Unlock', 'Backup folder gets locked when a backup operation is in progress.
            You should never manually unlock the backup folder unless something has gone wrong in the last operation and its stuck.'
                , 'unlock');

        $this->viewModel->setTemplate('application/db-backup/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;

    }

    public function newAction()
    {
        $model = new Model\DbBackup();
        $form = prepareForm(new Form\DbBackup(), array('submit-new'));

        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            $result = true;
            if (isset($post['buttons']['submit'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    $result = $this->createAction($model);
                    if ($result) {
                        $this->flashMessenger()->addSuccessMessage('New Backup Created.');
                    } else {
                        $this->flashMessenger()->addErrorMessage('Unable to create backup at this moment !.' . $this->getApi()->error);
                    }
                }
            }
            return $this->indexAction();
        }

        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        $this->viewModel->setTemplate('application/db-backup/new');
        return $this->viewModel;
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if (!is_array($id))
                $id = array($id);
            if ($id) {
                $backups = $this->getTable()->getAll(array('id' => $id));
                foreach ($backups as $backup) {
                    $this->getApi()->delete($backup->file, $backup->size);
                }
                $this->getTable()->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function restoreAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('admin/backup/db');

        $backup = $this->getTable()->get($id);
        $result = $this->getApi()->restore($backup->file);
        if ($result) {
            $backup->restored = time();
            $this->getTable()->save($backup);
            $this->flashMessenger()->addSuccessMessage('Backup successfully restored.');
        } else {
            $this->flashMessenger()->addErrorMessage('Unable to restore backup at this moment!.' . $this->getApi()->error);
        }
        return $this->indexAction();
    }

    public function unlockAction()
    {
        $this->getApi()->unlock();
    }

    /**
     * @param bool $model Model\DbBackup
     * @return bool|JsonModel
     */
    public function createAction($model = false)
    {
        $comment = $this->params()->fromPost('comment', false);
        $cron = $model == false;
        $api = $this->getApi();
        $fileName = $api->backup();
        if ($fileName) {
            if (!$model)
                $model = new Model\DbBackup();
            $model->userId = current_user()->id;
            $model->created = time();
            $model->file = $fileName;
            $model->size = $api->fileSize;
            if ($comment)
                $model->comment = $comment;
            $this->getTable()->save($model);
        } else {
            $this->flashMessenger()->addErrorMessage($api->error);
        }
        if ($cron) {
            return new JsonModel(array('done' => true, 'file' => $fileName));
        }
        return true;
    }

    public function downloadAction()
    {
        $file = $this->params()->fromRoute('file', false);
        if (!$file) {
            $this->viewModel->setTemplate('application/db-backup/download');
            return $this->viewModel;
        }

        return $this->plugin('stream')->binaryFile(PRIVATE_FILE . '/backup/db/' . $file, $file . '.zip');
    }

    /**
     * @return Model\DbBackupTable
     */
    private function getTable()
    {
        return getSM('db_backup_table');
    }

    /**
     * @return Db
     */
    private function getApi()
    {
        return getSM('db_backup_api');
    }
}