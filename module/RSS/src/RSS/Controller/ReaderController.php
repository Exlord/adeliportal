<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace RSS\Controller;

use Application\Model\Config;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use RSS\Model\Reader;
use RSS\Model\ReaderTable;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class ReaderController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('rss_reader_table');
        $grid->route = 'admin/rss-reader';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('title', 'Title');
        $url = new Column('url', 'Url');
        $feedLimit = new Column('feedLimit', 'Feed Limit');

        $lastRead = new Custom('lastRead', 'Last Read', function (Custom $col) {
            if ($col->dataRow->lastRead)
                return dateFormat($col->dataRow->lastRead);
            else
                return t('Never');
        });

        $readInterval = new Select('readInterval', 'Read Interval', ReaderTable::$readInterval);

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $title, $url, $feedLimit, $lastRead, $readInterval, $edit, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('New RSS');

        $this->viewModel->setTemplate('rss/reader/index');
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
        $form->setAction(url('admin/rss-reader/new'));

        if (!$model)
            $model = new Reader();
        else
            $form->setAction(url('admin/rss-reader/edit', array('id' => $model->id)));
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    $this->getTable()->save($model);
                    $this->flashMessenger()->addSuccessMessage('New RSS Reader Created Successfully.');
                    $this->indexAction();
                } else
                    $this->formHasErrors();
            } elseif (isset($post['buttons']['cancel'])) {
                $this->indexAction();
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

    /**
     * @return ReaderTable
     */
    private function getTable()
    {
        return getSM('rss_reader_table');
    }



}
