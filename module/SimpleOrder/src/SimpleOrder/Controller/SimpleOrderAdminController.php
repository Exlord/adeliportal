<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SimpleOrder\Controller;

use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use Mail\API\Mail;
use SimpleOrder\Form\Config;
use SimpleOrder\Model\SimpleOrderTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class SimpleOrderAdminController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('simple_order_table');
        $grid->route = 'admin/simple-order';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $name = new Column('name', 'Name');
        $mobile = new Column('mobile', 'Mobile');
        $email = new Column('email', 'Email');
        $description = new Column('description', 'Description');
        $created = new Date('created', 'application_label_reg_date');

        $showItem = new Custom('catItems', 'Show', function (Column $col) {
            $post = unserialize($col->dataRow->catItems);
            $html = '';
            if (isset($post['orderCategory']) && !empty($post['orderCategory'])) {
                foreach ($post['orderCategory'] as $row) {
                    if (isset($row['categoryItem']))
                        $itemId[] = $row['categoryItem'];
                    if (isset($row['subCategoryItem']))
                        $itemId[] = $row['subCategoryItem'];
                }
                $selectItemName = getSM('category_item_table')->getAll(array('id' => $itemId));
                if ($selectItemName->count()) {
                    foreach ($selectItemName as $row)
                        $itemName[$row->id] = $row->itemName;
                }
                foreach ($post['orderCategory'] as $row) {
                    if (isset($itemName[$row['categoryItem']]))
                        $html .= $itemName[$row['categoryItem']] . ' --> ';
                    if (isset($itemName[$row['subCategoryItem']]))
                        $html .= $itemName[$row['subCategoryItem']].' ';
                    if (isset(SimpleOrderTable::$quantity[$row['quantity']]))
                        $html .= ' [ '.t(SimpleOrderTable::$quantity[$row['quantity']]).' = ';
                    $html .= ' '.$row['count'].' ] ';
                    $html .= '<br/>';
                }
            }
            return '<span data-tooltip="' . $html . '" class="show-comment-icon" ></span>';
        }, array('headerAttr' => array('width' => '34px'), 'attr' => array('align' => 'center')));

        $del = new DeleteButton();

        $grid->addColumns(array($id, $name, $mobile, $email, $description, $created, $showItem,$del));
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('simple-order/simple-order-admin/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
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
        $config = getConfig('simple_config');
        $form = prepareConfigForm(new \SimpleOrder\Form\Config());
        if ($config->varValue)
            $form->setData($config->varValue);
        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($post);
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Simple Order Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Simple Order configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('simple-order/simple-order-admin/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    /**
     * @return SimpleOrderTable
     */
    private function getTable()
    {
        return getSM('simple_order_table');
    }
}
