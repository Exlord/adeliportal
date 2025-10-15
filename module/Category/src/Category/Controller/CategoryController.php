<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Category\Controller;


use Category\Model\Category;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use System\Controller\BaseAbstractActionController;
use Zend\Navigation\Page\Mvc;
use Zend\View\Model\JsonModel;

class CategoryController extends BaseAbstractActionController
{
    public function indexAction()
    {
        
        /*$pagination = $this
            ->getServiceLocator()
            ->get('category_table')
            ->getAll(null, 'id ASC', null, $this->params()->fromRoute('page', 1));*/

        $url_helper = $this->url();
        $grid = new DataGrid('category_table');
        $grid->route = 'admin/category';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px')));
        $grid->setIdCell($id);
        //   $catName = new Column('catName', 'Name');
        $catText = new Column('catText', 'Category Description');
        /* $search = new Button('View and Edit This Categories Items', Button::BUTTON_SEARCH,
             function ($dataRow) use ($url_helper) {
                 return $url_helper->fromRoute('admin/category/items', array('catId' => $dataRow->id));
             }
         );*/

        $catName = new Custom('catName', 'Name', function (Column $col) {
            $select = getSM()->get('category_item_table')->getItemsFirstLevelByMachineName($col->dataRow->catMachineName);
            $i = 0;
            if ($select) {
                $i = count($select);
            }
            return '<span class="category-title" >' . $col->dataRow->catName . '</span><span class="label label-red" >' . localizedDigits($i) . '</span>';
        });

        $view = new Button('Items', function (Button $col) {
            $col->route = 'admin/category/items';
            $col->routeParams = array('catId' => $col->dataRow->id);
            $col->icon = 'glyphicon glyphicon-th-list text-info';
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('ajax_page_load', 'btn', 'btn-default'))
        ));

//        $view = new Custom('view', 'View', function (Column $col) {
//            return ' <a class="grid_button search_button" href="' . url('admin/category/items', array('catId' => $col->dataRow->id)) . '" title="' . t('View and Edit This Categories Items') . '" role="button" aria-disabled="false">
//                         <span class="ui-button-icon-secondary ui-icon ui-icon-search"></span>
//                         </a>';
//        }, array('attr' => array('align' => 'center'), 'headerAttr' => array('width' => '40px')));


        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, $catName, $catText, $view, $edit, $delete));
        $grid->addNewButton('New Category');
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('category/category/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $form = new \Category\Form\Category();
        if (!$model) {
            $form->setAttribute('action', url('admin/category/new'));
            $model = new Category();
        } else {
            $form->setAttribute('action', url('admin/category/edit', array('id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $id = $this->getServiceLocator()->get('category_table')->save($model);
                    db_log_info("new category with id:$id is created");
                    $this->flashMessenger()->addSuccessMessage("New Category created Successfully");
                    if (!isset($post['buttons']['submit-new']))
                        return $this->indexAction();
                    else {
                        $model = new Category();
                        $form->bind($model);
                    }
                }
            }
        }

        $this->viewModel->setTemplate('category/category/new');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM('category_table')->get($id);
        return $this->newAction($model);
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                getSM('category_table')->remove($id);
                getSM('category_item_table')->removeAllByCategoryId($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function getCategoryListAction()
    {
        $term = $this->params()->fromPost('term');
        $data = getSM('category_table')->searchCategoryList($term);

        $json = array();
        foreach ($data as $row) {
            $json[] = array(
                'catId' => $row->id,
                'title' => $row->catName,
            );
        }
        return new JsonModel($json);
    }
}
