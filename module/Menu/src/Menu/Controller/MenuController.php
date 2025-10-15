<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Menu\Controller;

use DataView\Lib\Button;
use DataView\Lib\ButtonColumn;
use DataView\Lib\Column;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Grid;
use DataView\Lib\DataGrid;
use DataView\Lib\Select;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class MenuController extends BaseAbstractActionController
{

    public function indexAction()
    {
        $grid = new DataGrid('menu_table');
        $grid->route = 'admin/menu';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $name = new Column('menuName', 'Name');
        $title = new Column('menuTitle', 'Title');

        $showCommentIcon = new Button('Items', function (Button $col) {
            $col->route = 'admin/menu/items';
            $col->routeParams = array('menuId' => $col->dataRow->id);
            $col->icon = 'glyphicon glyphicon-th-list text-info';
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('ajax_page_load', 'btn', 'btn-default'))
        ));

        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, $name, $title, $showCommentIcon, $edit, $delete));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('menu/menu/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $form = new \Menu\Form\Menu();
        if (!$model) {
            $model = new \Menu\Model\Menu();
            $form->setAttribute('action', url('admin/menu/new'));
        } else {
            $form->setAttribute('action', url('admin/menu/edit', array('id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
        }

        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {

                $form->setData($post);

                if ($form->isValid()) {

                    $id = getSM()->get('menu_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction();
                    }
                }
            }
        }

        $this->viewModel->setTemplate('menu/menu/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('menu_table')->get($id);

        return $this->newAction($model);
    }

    public function updateAction()
    {
        /* if ($this->request->isPost()) {
             $field = $this->params()->fromPost('field', false);
             $value = $this->params()->fromPost('value', false);
             $id = $this->params()->fromPost('id', 0);
             if ($id && $field && has_value($value)) {
                 if ($field == 'itemStatus') {
                     $this->getServiceLocator()->get('links_table')->update(array($field => $value), array('id' => $id));
                     return new JsonModel(array('status' => 1));
                 }
             }
         }
         return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));*/
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getServiceLocator()->get('menu_table')->remove($id);
                $this->getServiceLocator()->get('menu_item_table')->removeByMenuId($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

}
