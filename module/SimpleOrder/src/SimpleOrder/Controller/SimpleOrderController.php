<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SimpleOrder\Controller;

use Mail\API\Mail;
use SimpleOrder\Model\SimpleOrderTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\ViewModel;

class SimpleOrderController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $form = new \SimpleOrder\Form\SimpleOrder();
        $model = new \SimpleOrder\Model\SimpleOrder();
        $form->setAction(url('app/simple-order'));
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['submit'])) {
                $form->setData($post);
                if ($form->isValid()) {

                    $model->created = time();
                    $model->userId = current_user()->id;
                    $model->catItems = serialize($post->category_collection);
                    getSM('simple_order_table')->save($model);

                    //send notify
                    $config = getConfig('simple_config')->varValue;
                    $notify = getNotifyApi();
                    if ($notify) {
                        //render items
                        if (isset($post->category_collection['orderCategory']) && !empty($post->category_collection['orderCategory'])) {
                            foreach ($post->category_collection['orderCategory'] as $row) {
                                if (isset($row['categoryItem']))
                                    $itemId[] = $row['categoryItem'];
                                if (isset($row['subCategoryItem']))
                                    $itemId[] = $row['subCategoryItem'];
                            }
                        }
                        $selectItemName = getSM('category_item_table')->getAll(array('id' => $itemId));
                        if ($selectItemName->count()) {
                            foreach ($selectItemName as $row)
                                $itemName[$row->id] = $row->itemName;
                        }
                        $quantity = array();
                        foreach (SimpleOrderTable::$quantity as $key => $val)
                            $quantity[$key] = t($val);

                        $viewModel = new ViewModel();
                        $viewModel->setTerminal(true);
                        $viewModel->setTemplate('simple-order/simple-order-admin/render-items');
                        $viewModel->setVariables(array(
                            'categories' => $post->category_collection['orderCategory'],
                            'itemName' => $itemName,
                            'quantity' => $quantity,
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
                        $notify->notify('SimpleOrder', 'simpleOrder_new_order_form', $params);
                    }
                    //end

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setTemplate('simple-order/simple-order/index');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }
}
