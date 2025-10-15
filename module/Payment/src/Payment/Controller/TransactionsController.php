<?php
namespace Payment\Controller;

use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;


class TransactionsController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('transactions_table');
        $grid->route = 'admin/payment/transactions';
        $where = array();
        if (!isAllowed(\Payment\Module::ADMIN_PAYMENT_TRANSACTIONS_VIEW_ALL))
            $where['userId'] = current_user()->id;
        $grid->getSelect()->where($where);
        // $userId = new Column('userId', 'User Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $amount = new Column('amount', 'Amount');
        $note = new Column('note', 'Note');
        $date = new Date('date', 'Date', array(), 0, 1);
        // $adminId = new Column('adminId', 'Admin Id');

        $grid->addColumns(array($amount, $note, $date /*, $adminId*/));
        //  $grid->addNewButton();
        // $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('payment/transactions/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction()
    {
        $userId = $this->params()->fromQuery('userId');
        $form = new \Payment\Form\Transactions();
        $form->setAttribute('action', url('admin/payment/transactions/new'));
        $form->get('buttons')->remove('submit-new');
        $model = new \Payment\Model\Transactions();
        $model->userId = $userId;
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->redirect()->toRoute('admin/payment/transactions');
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);

                if ($form->isValid()) {

                    if (current_user()->id == $model->userId)
                        $adminId = 0;
                    else
                        $adminId = current_user()->id;

                    if (isset($post['type'])) {
                        switch ($post['type']) {
                            case 2:
                                $model->amount = $model->amount * -1;
                                break;
                        }
                    }

                    if ($model->amount && $model->amount > 0) {
                        if (isAllowed(\Payment\Module::ADMIN_PAYMENT_TRANSACTIONS_NEW_DIRECT_DEPOSIT)) {
                            $params = array(
                                'userId' => $model->userId,
                                'amount' => $model->amount,
                                'note' => $model->note,
                                'date' => time(),
                                'adminId' => $adminId,
                            );
                            return $this->validateTransactions($params);
                        } else {
                            //payment
                            $paymentParams = array(
                                'amount' => $model->amount,
                                'email' => '',
                                'comment' => 'Pay For Increase money for account',
                                'validate' => array(
                                    'route' => 'app/transaction/validate-transactions',
                                    'params' => array(
                                        'userId' => $model->userId,
                                        'note' => $model->note,
                                        'adminId' => $adminId,
                                        'amount' => $model->amount,
                                        'transactions'=>1,
                                    ),
                                )
                            );
                            $paymentParams = serialize($paymentParams);
                            $paymentParams = base64_encode($paymentParams);
                            return $this->redirect()->toRoute('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
                            //end
                        }
                    } elseif ($model->amount && $model->amount < 0) {
                        $params = array(
                            'userId' => $model->userId,
                            'amount' => $model->amount,
                            'note' => $model->note,
                            'date' => time(),
                            'adminId' => $adminId,
                        );
                        return $this->validateTransactions($params);
                        //TODO CREATE FACTOR
                    }

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->redirect()->toRoute('admin/payment/transactions');
                    }
                }
            }
        }

        $this->viewModel->setTemplate('payment/transactions/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function validateTransactions($modelArray = null)
    {
        if (!$modelArray) {
            $params = $this->params()->fromRoute('params');
            $paymentId = $this->params()->fromRoute('paymentId');
            $params = unserialize(base64_decode($params));
            if ($paymentId) {
                $modelArray = $params;
            }
        }
        if ($modelArray)
            $result = getSM('transactions_api')->insertTransactions($modelArray);
        $this->flashMessenger()->addSuccessMessage(t('PAYMENT_NEW_TRANSACTION_SUCCESS'));
        return $this->forward()->dispatch('User\Controller\User', array('action' => 'list'));
    }

    public function configAction()
    {
        $config = getSM('config_table')->getByVarName('transactions_config');
        $form = prepareConfigForm(new \Payment\Form\Config());
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Transactions Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Transactions configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('payment/transactions/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }
}
