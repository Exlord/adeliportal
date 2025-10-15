<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Links\Controller;

use Application\API\Breadcrumb;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class ItemsController extends \System\Controller\BaseAbstractActionController
{

    public function indexAction()
    {
        $categoryItems = $this
            ->getServiceLocator()
            ->get('category_item_table')
            ->getItemsTreeByMachineName('links_category');
        $grid = new DataGrid('links_table');
        $grid->route = 'admin/links';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $name = new Column('itemName', 'Name');
        $title = new Column('itemTitle', 'Title');
        $link = new Column('itemLink', 'Url');

        $status = new Select('itemStatus', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $edit = new EditButton();
        $delete = new DeleteButton();

        $groupFilter = new Column('catId', 'Categories');
        $groupFilter->selectFilterData = $categoryItems;

        $grid->addColumns(array($id, $name, $title, $link, $status, $edit, $delete));
        $grid->setSelectFilters(array($groupFilter));
        $grid->addNewButton();
        $grid->addNewButton('Categories', 'Categories', false, 'admin/category');
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('links/items/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $form = new \Links\Form\Items();
        $categoryItems = $this
            ->getServiceLocator()
            ->get('category_item_table')
            ->getItemsTreeByMachineName('links_category');

        $form->get('catId')->setValueOptions($categoryItems);


        if (!$model) {
            $model = new \Links\Model\LinksItem();
            $form->setAttribute('action', url('admin/links/new'));
        } else {
            $form->setAttribute('action', url('admin/links/edit', array('id' => $model->id)));
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

                    $id = getSM()->get('links_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction();
                    }
                }
            }
        }

        $this->viewModel->setTemplate('links/items/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('links_table')->get($id);
        $this->viewModel->setTemplate('links/items/new');
        return $this->newAction($model);
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
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
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getServiceLocator()->get('links_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function viewAction()
    {
        Breadcrumb::AddMvcPage('Links', 'app/links');

        $catId = $this->params()->fromRoute('catId', false);

        if ($catId !== false) {
            $cats = $this->getCategoryItemTable()->getItemsByMachineName('links_category', $catId);
            if ($cats->count()) {
                $this->viewModel->setVariable('cats', $cats);
            }

            $tagsParents = array_reverse($this->getCategoryItemTable()->getParents($catId));
            foreach ($tagsParents as $pTag)
                Breadcrumb::AddMvcPage($pTag->itemName, 'app/links/category', array('catId' => $pTag->id, 'catName' => $pTag->itemName));
        }
        $links = $this->getLinksTable()->getAll(array('catId' => $catId, 'itemStatus' => 1), array('itemOrder DESC', 'itemTitle ASC'));
        if ($links->count())
            $this->viewModel->setVariable('links', $links);

        $this->viewModel->setTemplate('links/items/view');
        return $this->viewModel;
    }

    public function categoryListAction()
    {
        $categoryItems = $this
            ->getServiceLocator()
            ->get('category_item_table')
            ->getItemsTreeByMachineName('links_category');

        $json = array();
        foreach ($categoryItems as $key => $item) {
            $json[] = array('catId' => $key, 'catName' => $item);
        }
        return new JsonModel($json);
    }

    /**
     * @return \Links\Model\LinksItemTable;
     */
    private function getLinksTable()
    {
        return getSM()->get('links_table');
    }

}
