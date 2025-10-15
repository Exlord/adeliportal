<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Category\Controller;

use Category\Model\CategoryItem;
use Category\Model\CategoryItemTable;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class ItemsController extends BaseAbstractActionController
{
    public function indexAction($catId = null)
    {
        if (!$catId)
            $catId = $this->params('catId', 1);

        $parentId = $this->params('parentId', 0);
        $grid = new DataGrid('category_item_table');
        $grid->getSelect()->where(array('catId' => $catId, 'parentId' => $parentId))->order('id ASC');
        $grid->route = 'admin/category/items';
        $grid->routeParams = array('catId' => $catId, 'parentId' => $parentId);
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $name = new Column('itemName', 'Name');
        $desc = new Custom('itemText', 'Description', function (Custom $col) {
            $str = strip_tags($col->dataRow->itemText);
            if (strlen($str) > 100) {
                $str = mb_substr($str, 0, 100, 'UTF-8') . ' ...';
            }
            return $str;
        });

        $status = new Select('itemStatus', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $view = new Button('Items', function (Button $col) {
            $col->route = 'admin/category/items';
            $col->routeParams = array('catId' => $col->dataRow->catId, 'parentId' => $col->dataRow->id);
            $col->icon = 'glyphicon glyphicon-th-list text-info';
        }, array(
            'headerAttr' => array('width' => '34px', 'title' => t('View this items childes')),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('ajax_page_load', 'btn', 'btn-default'))
        ));

        $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $name, $desc, $status, $view, $edit, $delete));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('category/items/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;

    }

    public function newAction($model = null, $imagesFile = array())
    {
        $catId = $this->params('catId');
        $parentId = $this->params('parentId');
        $form = new \Category\Form\Items();
        $action = '';
        $item = null;
        if ($model) {
            $item = $model;
            $form->get('buttons')->remove('submit-new');
            $action = url('admin/category/items/edit', array('catId' => $catId, 'id' => $model->id));
        } else {
            $item = new CategoryItem();
            $item->catId = $catId;
            $item->parentId = $parentId;
            $action = url('admin/category/items/new', array('catId' => $catId));
        }

        $form->bind($item);
        $form->setAction($action);
        $form->setAttribute('data-cancel', url('admin/category/items', array('catId' => $catId)));

        $items = $this
            ->getServiceLocator()
            ->get('category_item_table')
            ->getItemsTree($catId);

        $form->get('parentId')->setValueOptions($items);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction($catId);
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $post = array_merge_recursive(
                    $this->request->getPost()->toArray(),
                    $this->request->getFiles()->toArray()
                );

                $form->setData($post);
                if ($form->isValid()) {

                   // $imageValue = $item->image;
                    $imageValue = $this->request->getFiles()->toArray();
                    $id = $this->getItemTable()->save($item);

                    if ($id || $item->id) {
                        if (!$id)
                            $id = $item->id;
                        //upload image

                        if (isset($imageValue['image']['image'])) {
//                            $imageOptions = $post['image']['image'];
                            $imageArray = $imageValue['image']['image'];

                            if ($imageArray) {
                                $files = array();
                                foreach ($imageArray as $key => $row) {
                                    if (isset($row['imageValue']['name']) && isset($row['imageValue']['tmp_name']) && !empty($row['imageValue']['tmp_name'])) {
                                        if (isset($row['imageTitle']))
                                            $files[$key]['title'] = $row['imageTitle'];
                                        if (isset($row['imageAlt']))
                                            $files[$key]['alt'] = $row['imageAlt'];
                                        $files[$key]['tmp_name'] = $row['imageValue']['tmp_name'];
                                        $files[$key]['name'] = $row['imageValue']['name'];
                                    }
                                }
                                $this->getFileApi()->save(\Category\Module::ENTITY_TYPE_CATEGORY_ITEM, $id, $files, 100);
                            }
                        }
                        //end
                    }

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    db_log_info("new category item with id:$id is created");
                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction($catId);
                    } else {
                        $item = new CategoryItem();
                        $form->bind($item);
                        $items = $this
                            ->getServiceLocator()
                            ->get('category_item_table')
                            ->getItemsTree($catId);

                        $form->get('parentId')->setValueOptions($items);
                    }
                }
            }
        }

        $this->viewModel->setTemplate('category/items/new');
        $this->viewModel->setVariables(array('form' => $form, 'imagesFile' => $imagesFile));
        return $this->viewModel;
    }

    public function editAction()
    {
        $catId = $this->params()->fromRoute('catId', false);
        $id = $this->params()->fromRoute('id', false);
        if (!$id || !$catId)
            return $this->invalidRequest('admin/category/items', array('catId' => $catId));

        $item = $this->getItemTable()->get($id);
        if ($item) {
            $imagesFile = getSM('file_table')->getByEntityType(\Category\Module::ENTITY_TYPE_CATEGORY_ITEM, $id, true);
            return $this->newAction($item, $imagesFile);
        } else
            return $this->invalidRequest('admin/category/items', array('catId' => $catId));
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field && has_value($value)) {
                if ($field == 'itemStatus') {
                    $this->getServiceLocator()->get('category_item_table')->update(array($field => $value), array('id' => $id));
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
                $this->getItemTable()->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    /**
     * @return CategoryItemTable
     */
    private function getItemTable()
    {
        return getSM('category_item_table');
    }

    public function getItemListAction()
    {
        /*$data = $this->params()->fromQuery();
        $data = getSM('category_item_table')->searchItemList($data);

        $json = array();
        foreach ($data as $key => $val) {
            $json[] = array(
                'id' => $key,
                'title' => $val,
            );
        }
        return new JsonModel($json);*/ // baraye zamani ke az auto complete estefade kardim budesh


        $data = $this->params()->fromPost();
        if (isset($data['catId'])) {
            $data = getSM('category_item_table')->getAll(array('catId' => $data['catId']));
            $this->viewModel->setTerminal(true);
            $this->viewModel->setTemplate('category/items/select_item_list');
            $this->viewModel->setVariables(array(
                'data' => $data,
            ));
            $html = $this->render($this->viewModel);

        } else {
            $html = '';
        }

        $html = "<option>" . t('-- Select --') . "</option>" . $html;
        return new JsonModel(array(
            'html' => $html
        ));
    }

    public function subCategoryAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            if (isset($data['parentId']) && $data['parentId']) {
                $cat = getSM('category_table')->getByMachineName('simpleOrder');
                if ($cat->id) {
                    $category = getSM('category_item_table')->getItemsTree($cat->id, $data['parentId']);
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('category/items/sub-category');
                    $this->viewModel->setVariables(array(
                        'category' => $category,
                    ));
                    $html = $this->render($this->viewModel);
                    return new JsonModel(array(
                        'status' => 1,
                        'html' => $html,
                    ));
                }
            }
        }
        return New JsonModel(array(
            'status' => 0,
        ));
    }

    public function catItemListViewBlockAction()
    {
        $html = '';
        $catId = $this->params()->fromRoute('catId', 0);
        $catItemId = $this->params()->fromRoute('catItemId', 0);
        if ($catId && $catItemId) {
            $category = getSM('category_table')->get($catId);
            if (!$category)
                return t('Invalid category is selected in block setting !');
            $options = array(
                'imageWidth' => 100,
                'imageHeight' => 100,
                'resizeType' => 'fix',
                'titleType' => 'normal',
                'positionType' => 'vertical',
                'countLevel' => 1,
            );
            $select = getSM('category_item_table')->getAllItemList($catId, $catItemId, 1);
            $html = getSM('category_item_list_api')->createCatItemList($select, $options, $category, 1, $catItemId);
        } else
            $this->flashMessenger()->addErrorMessage('CATEGORY_NOT_CATEGORY');
        $this->viewModel->setTemplate('category/helper/cat-item-list-block');
        $this->viewModel->setVariables(array(
            'html' => $html,
        ));
        return $this->viewModel;
    }

    public function searchAction()
    {
        $catId = $this->params()->fromRoute('catId', null);
        if ($this->request->isPost() && $catId) {
            $term = $this->params()->fromPost('q', false);
            $type = $this->params()->fromPost('type', 0);
            if ($term) {
                $page = $this->params()->fromPost('page', 1);
                $page_limit = $this->params()->fromPost('page_limit', 10);
                $result = $this->getItemTable()->search($term, $page, $page_limit);
                if ($result->count()) {
                    foreach ($result as $row) {
                        if ($type)
                            $data['items'][] = array(
                                'id' => $row->itemName,
                                'text' => trim($row->itemName),
                            );
                        else
                            $data['items'][] = array(
                                'id' => $row->id,
                                'text' => trim($row->itemName),
                            );
                    }
                    $data['total'] = $result->getTotalItemCount();
                    return new JsonModel($data);
                }
            }
        }
        $data['items'][] = array(
            'id' => 0,
            'text' => t('APP_NOT_FOUND'),
        );
        $data['total'] = 0;
        return new JsonModel($data);

    }
}
