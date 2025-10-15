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
use Zend\View\Model\ViewModel;

class StepOrderController extends BaseAbstractActionController
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
                        $html .= $itemName[$row['subCategoryItem']] . ' ';
                    if (isset(SimpleOrderTable::$quantity[$row['quantity']]))
                        $html .= ' [ ' . t(SimpleOrderTable::$quantity[$row['quantity']]) . ' = ';
                    $html .= ' ' . $row['count'] . ' ] ';
                    $html .= '<br/>';
                }
            }
            return '<span data-tooltip="' . $html . '" class="show-comment-icon" ></span>';
        }, array('headerAttr' => array('width' => '34px'), 'attr' => array('align' => 'center')));

        $del = new DeleteButton();

        $grid->addColumns(array($id, $name, $mobile, $email, $description, $created, $showItem, $del));
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('simple-order/step-order/index');
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

    public function stepOrderAction()
    {
        $form = new \SimpleOrder\Form\SimpleOrder();
        $model = new \SimpleOrder\Model\SimpleOrder();
        $form->setAction(url('app/step-order'));
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['submit'])) {
                $form->setData($post);
                if ($form->isValid()) {

                    $model->created = time();
                    $model->userId = current_user()->id;
                    $model->catItems = serialize($post->orderCategory);
                    getSM('simple_order_table')->save($model);

                    //send notify
                    $config = getConfig('simple_config')->varValue;
                    $notify = getNotifyApi();
                    if ($notify) {
                        //render items
                        $viewModel = new ViewModel();
                        $viewModel->setTerminal(true);
                        $viewModel->setTemplate('simple-order/step-order/render-items');
                        $viewModel->setVariables(array(
                            'select' => $post->orderCategory,
                        ));
                        $htmlItems = $this->render($viewModel);
                        //end

                        if (isset($config['email']) && $config['email']) {
                            $email = $notify->getEmail();
                            $email->to = $config['email'];
                            $email->from = Mail::getFrom();
                            $email->subject = t('simpleOrder_new_order_form');
                            $email->entityType = \SimpleOrder\Module::ENTITY_TYPE;
                            $email->queued = 0;
                        }
                        $params = array(
                            '__NAME__' => $model->name,
                            '__MOBILE__' => $model->mobile,
                            '__EMAIL__' => $model->email,
                            '__DESCRIPTION__' => $model->description,
                            '__CREATED__' => $model->created,
                            '__ITEMS__' => $htmlItems,
                        );
                        //$notify->notify('StepOrder', 'simpleOrder_new_order_form', $params);
                    }
                    //end

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                } else
                    $this->formHasErrors();
            }
        }

        $select = getSM('category_item_table')->getItemsFirstLevelByMachineName('stepOrder');
        $this->viewModel->setTemplate('simple-order/step-order/step-order');
        $this->viewModel->setVariables(array(
            'select' => $select,
        ));
        return $this->viewModel;
    }

    public function otherStepAction()
    {
        $data = $this->params()->fromPost();
        $dataArray = array();
        if (isset($data['id']) && $data['id'] && isset($data['step']) && isset($data['info'])) {
            $html = '';
            if ((int)$data['step'] != 6) {
                $select = getSM('category_item_table')->getItemsFirstLevelByMachineName('stepOrder', $data['id']);
            } else {
                if (isset($data['info'])) {
                    $catItemId = array();
                    foreach ($data['info'] as $val)
                        $catItemId[] = $val['id'];
                    $select = getSM('category_item_table')->getAll(array('id' => $catItemId))->toArray();
                }
                $form = new \SimpleOrder\Form\SimpleOrder();
                $form->setAction(url('app/step-order'));
            }
            if ($select)
                foreach ($select as $row) {
                    $imagesFile = getSM('file_table')->getByEntityType(\Category\Module::ENTITY_TYPE_CATEGORY_ITEM, $row['id'], false);
                    $dataArray[$row['id']] = array(
                        'id' => $row['id'],
                        'itemText' => $row['itemText'],
                        'itemName' => $row['itemName'],
                        'images' => $imagesFile,
                    );
                }
            switch ((int)$data['step']) {
                case 1:
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('simple-order/step-order/step1');
                    $this->viewModel->setVariables(array(
                        'dataArray' => $dataArray,
                    ));
                    $html = $this->render($this->viewModel);
                    break;
                case 2:
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('simple-order/step-order/step2');
                    $this->viewModel->setVariables(array(
                        'dataArray' => $dataArray,
                        'info' => $data['info'],
                    ));
                    $html = $this->render($this->viewModel);
                    break;
                case 3:
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('simple-order/step-order/step3');
                    $this->viewModel->setVariables(array(
                        'dataArray' => $dataArray,
                        'info' => $data['info'],
                    ));
                    $html = $this->render($this->viewModel);
                    break;
                case 4:
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('simple-order/step-order/step4');
                    $this->viewModel->setVariables(array(
                        'dataArray' => $dataArray,
                        'info' => $data['info'],
                    ));
                    $html = $this->render($this->viewModel);
                    break;
                case 5:
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('simple-order/step-order/step5');
                    $this->viewModel->setVariables(array(
                        'dataArray' => $dataArray,
                        'info' => $data['info'],
                    ));
                    $html = $this->render($this->viewModel);
                    break;
                case 6:
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('simple-order/step-order/step6');
                    $this->viewModel->setVariables(array(
                        'form' => $form,
                        'catId' => serialize($data['info']),
                        'info' => $data['info'],
                        'dataArray' => $dataArray,
                    ));
                    $html = $this->render($this->viewModel);
                    break;
            }
            return new JsonModel(array(
                'html' => $html,
            ));
        }

    }
}
