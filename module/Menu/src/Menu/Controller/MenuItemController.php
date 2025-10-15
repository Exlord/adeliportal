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
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\DataGrid;
use File\API\File;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;


class MenuItemController extends BaseAbstractActionController
{

    public function indexAction($menuId = null)
    {
        if (!$menuId)
            $menuId = $this->params()->fromRoute('menuId', 0);
        $parentId = $this->params()->fromRoute('parentId', 0);
        if ($parentId) {
            $page = $this->setAdminBreadcrumb();
            $params = $page->getParams();
            $params['parentId'] = 0;
            $page->setParams($params);
            $params['parentId'] = $parentId;
            $this->addBreadcrumb($page, 'Child Items', $params);
        }

        $grid = new DataGrid('menu_item_table');
        $grid->route = 'admin/menu/items';
        $grid->routeParams = array('menuId' => $menuId);
        if ($parentId)
            $grid->routeParams['parentId'] = $parentId;
        $select = $grid->getSelect();
        $select->where(array('menuId' => $menuId, 'parentId' => $parentId));

        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $name = new Column('itemName', 'Name');
        $title = new Column('itemTitle', 'Title');
        $Url = new Custom('', 'Url', function (Column $col) {
            $col->dataRow->itemUrlTypeParams = unserialize($col->dataRow->itemUrlTypeParams);
            $api = 'Menu\API\Menu';
            if (isset($col->dataRow->itemUrlTypeParams[$col->dataRow->itemUrlType]['api']))
                $api = $col->dataRow->itemUrlTypeParams[$col->dataRow->itemUrlType]['api'];

            $api = str_replace('\\\\', '\\', $api);
            $url = $api::getMenuUrl($col->dataRow->itemUrlTypeParams);
            return sprintf("<a rel='tooltip' title='%s' href='%s'>%s</a>", $col->dataRow->itemTitle, $url, $col->dataRow->itemName);
        });

        $showChild = new Button('Items', function (Button $col) {
            $col->route = 'admin/menu/items';
            $col->routeParams = array('menuId' => $col->dataRow->menuId, 'parentId' => $col->dataRow->id);
            $col->icon = 'glyphicon glyphicon-th-list text-info';
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('btn', 'btn-default', 'ajax_page_load'))
        ));

        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, $name, $title, $Url, $showChild, $edit, $delete));

        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('menu/menu-item/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = false, $menuId = null)
    {
        if (!$menuId)
            $menuId = $this->params()->fromRoute('menuId', 0);
        $menus = getSM()->get('menu_table')->getArray();
        $menu_items = getSM()->get('menu_item_table')->getArray($menuId);
        $form = new \Menu\Form\MenuItem($menus, $menu_items);
        $oldImage = false;
        $menuItemId = '';
        if (!$model) {
            $model = new \Menu\Model\MenuItem();
            $model->menuId = $menuId;
            $form->setAttribute('action', url('admin/menu/items/new', array('menuId' => $menuId)));
        } else {
            $form->setAttribute('action', url('admin/menu/items/edit', array('menuId' => $model->menuId, 'id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
            $oldImage = $model->image;
            $menuItemId = $model->id;
        }
        $form->setAttribute('data-cancel', url('admin/menu/items', array('menuId' => $menuId)));

        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction($menuId);
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $post = array_merge_recursive(
                    $this->request->getPost()->toArray(),
                    $this->request->getFiles()->toArray()
                );
                $form->setData($post);

                if ($form->isValid()) {

                    $imageFile = $model->image;
                    $image = '';
                    if (isset($imageFile['tmp_name']) && !empty($imageFile['tmp_name'])) {
                        if ($oldImage)
                            @unlink(ROOT . $oldImage);
                        $image = File::MoveUploadedFile($imageFile['tmp_name'], PUBLIC_FILE . '/menuItem', $imageFile['name']);
                    } elseif ($oldImage)
                        $image = $oldImage;

                    $model->image = $image;

                    $id = getSM()->get('menu_item_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");


                    if (isset($post['buttons']['submit-new'])) {
                        $form->setData(array());
                    } else
                        return $this->indexAction($menuId);
                }
            }
        }
        $this->viewModel->setTemplate('menu/menu-item/new');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'menuTypes' => $form->menuTypes,
            'oldImage' => $oldImage,
            'menuId' => $menuId,
            'menuItemId' => $menuItemId,
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $menuId = $this->params()->fromRoute('menuId', 0);
        $model = getSM()->get('menu_item_table')->get($id);
        $model->menuId = $menuId;
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
                $select = getSM('menu_item_table')->get($id);
                foreach ($select as $row)
                    if (isset($row->image) && $row->image)
                        @unlink(PUBLIC_PATH . $row->image);
                getSM('menu_item_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function deleteImgAction()
    {
        $itemId = $this->params()->fromPost('itemId');
        if ($itemId) {
            $selectItem = getSM('menu_item_table')->get($itemId);
            if ($selectItem) {
                $image = $selectItem->image;
                @unlink(ROOT . $image);
                getSM('menu_item_table')->update(array('image' => null), array('id' => $itemId));
                return new JsonModel(array(
                    'status' => 1
                ));
            }
        }
        return new JsonModel(array(
            'status' => 0
        ));
    }

}
