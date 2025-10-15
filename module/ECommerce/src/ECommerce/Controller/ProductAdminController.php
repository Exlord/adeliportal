<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ECommerce\Controller;

use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use DataView\Lib\Visualizer;
use ECommerce\API\Product;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;

class ProductAdminController extends BaseAbstractActionController
{
    /**
     * @return \ECommerce\API\Product
     */
    private function getApi()
    {
        return getSM('product_api');
    }

    private function getCategories()
    {
        return $this->getCategoryItemTable()->getItemsTreeByMachineName('commerce_product');
    }

    public function indexAction()
    {
        $this->getApi()->loadExtraTypes();

        $grid = new DataGrid('product_table');
        $grid->route = 'admin/e-commerce/product';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('name', 'Title');
        $description = new Custom('note', 'Description', function (Custom $col) {
            $str = strip_tags($col->dataRow->note);
            if (strlen($str) > 100) {
                $str = mb_substr($str, 0, 100, 'UTF-8') . ' ...';
            }
            return $str;
        });

        $position = new Custom('price', 'Price', function (Custom $col) {
            $price = trim($col->dataRow->price);
            if (!empty($price)) {
                $cf = getVHM('currencyFormat');
                if ($cf)
                    $price = $cf($price);
            }
            return $price;
        });

        $status = new Visualizer('type', 'Type',
            array(),
            Product::$types,
            array('headerAttr' => array('width' => '100px')));

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $description, $title, $position, $status, $edit, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('New Product');

        $this->viewModel->setTemplate('e-commerce/product-admin/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $this->getApi()->loadExtraTypes();
        $form = new \ECommerce\Form\Product($this->getCategories());
        if (!$model) {
            $model = array();
            $form->setAction(url('admin/e-commerce/product/new'));
        } else {
            $form->setAction(url('admin/e-commerce/product/edit', array('id' => $model['id'])));
            $form = prepareForm($form, array('submit-new'));
        }
        $form->setData($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    $this->getTable()->save($model);
                    $this->flashMessenger()->addSuccessMessage('New RSS Reader Created Successfully.');
                    $this->redirect()->toRoute('admin/rss-reader');
                } else
                    $this->formHasErrors();
            } elseif (isset($post['buttons']['cancel'])) {
                $this->redirect()->toRoute('admin/rss-reader');
            }
            if (isset($post['buttons']['submit-new'])) {
                $model = new \Mail\Model\Template();
                $form->bind($model);
            }
        }

        $this->viewModel->setTemplate('e-commerce/product-admin/new');
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

    public function configAction()
    {
        /* @var $config Config */
        $config = getConfig('newsletter');
        $form = prepareConfigForm(new \RSS\Form\Config($tagId));
        $form->setData($config->varValue);


        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getConfigTable()->save($config);
                    db_log_info("Newsletter Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Newsletter Text configs saved successfully');
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    /**
     * @return ReaderTable
     */
    private function getTable()
    {
        return getSM('rss_reader_table');
    }
}
