<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ProductShowcase\Controller;

use Application\API\Breadcrumb;
use DataView\Lib\DataGrid;
use ProductShowcase\Model\PsTable;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class PsController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $ajaxLoaded = false;
        $showTags = false;
        $page = $this->params()->fromQuery('page', 1);
        $pageCount = $this->params()->fromPost('page', 0);
        $itemCount = $this->params()->fromQuery('per_page', 20);
        $catId = $this->params()->fromRoute('tagId', 0 /*, $page, $itemCount*/);
        if ($pageCount) {
            $showTags = false;
            $page = $pageCount;
            $ajaxLoaded = true;
        }
        $route = 'app/product-showcase';
        $routeParams = array(
            'catId' => $catId,
        );

        $tagsParents = array_reverse($this->getCategoryItemTable()->getParents($catId));
        Breadcrumb::AddMvcPage(t('PS_PRODUCT_SHOWCASE'), 'app/product-showcase');

        foreach ($tagsParents as $pTag) {
            Breadcrumb::AddMvcPage($pTag->itemName, 'app/product-showcase/list', array('tagId' => $pTag->id, 'tagName' => $pTag->itemName));
        }
        $selectCat = getSM('category_table')->getByMachineName(\ProductShowcase\Module::PS_ENTITY_TYPE);
        $selectCatItem = getSM('category_item_table')->getItemsByParentId($catId, $selectCat->id);
        $currentCat = null;
        if ($catId)
            $currentCat = getSM('category_item_table')->get($catId);

        $fieldsTable = $this->getFieldsApi()->init('product_showcase');
        $fields = $this->getFieldsTable()->getByEntityType(\ProductShowcase\Module::PS_ENTITY_TYPE)->toArray();

        $paginator = $this->getTable()->getDataArray($catId, $fieldsTable, $fields, $page, $itemCount);
        $this->viewModel->setTemplate('product-showcase/product-showcase/index');
        $this->viewModel->setVariables(array(
            'paginator' => $paginator,
            'ajaxLoaded' => $ajaxLoaded,
            'showTags' => $showTags,
            'route' => $route,
            'routeParams' => $routeParams,
            'selectCatItem' => $selectCatItem,
            'currentCat' => $currentCat,
            'fields' => $fields,
            'per_page' => $itemCount,
        ));
        if ($ajaxLoaded) {
            $html = $this->render($this->viewModel);
            return new JsonModel(array(
                'html' => $html
            ));
        }
        return $this->viewModel;
    }

    public function viewAction()
    {
        $id = $this->params()->fromRoute('id', null);
        if (!$id)
            return $this->invalidRequest('app/product-showcase');

        $tagName = getSM('entity_relation_table')->getItems($id, \ProductShowcase\Module::PS_ENTITY_TYPE);
        $fields_table = getSM()->get('fields_api')->init(\ProductShowcase\Module::PS_ENTITY_TYPE);
        $files = getSM('file_table')->getByEntityType(\ProductShowcase\Module::PS_ENTITY_TYPE, $id);
        $fields = $this->getFieldsTable()->getByEntityType(\ProductShowcase\Module::PS_ENTITY_TYPE)->toArray();
        $data = $this->getTable()->getById($id, $fields_table, $fields);

        if (!$data) {
            $this->flashMessenger()->addErrorMessage('Item with requested id not found');
            return $this->redirect()->toRoute('app/product-showcase');
        }

        $this->viewModel->setTemplate('product-showcase/product-showcase/view');
        $this->viewModel->setVariables(array(
            'data' => $data,
            'fields' => $fields,
            'tagName' => $tagName,
            'files' => $files,
        ));
        return $this->viewModel;
    }

    public function categoryAction()
    {
        $catId = 0;
        $selectCat = getSM('category_table')->getByMachineName(\ProductShowcase\Module::PS_ENTITY_TYPE);
        $selectCatItem = getSM('category_item_table')->getItemsByParentId($catId, $selectCat->id);
        $this->viewModel->setTemplate('product-showcase/product-showcase/category');
        $this->viewModel->setVariables(array(
            'selectCatItem' => $selectCatItem,
        ));
        return $this->viewModel;
    }

    /**
     * @return PsTable
     */
    private function getTable()
    {
        return getSM('product_showcase_table');
    }
}
