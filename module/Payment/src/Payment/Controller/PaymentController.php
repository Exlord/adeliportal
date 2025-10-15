<?php
namespace Payment\Controller;

use Application\API\App;
use Application\API\Breadcrumb;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\Select;
use Payment\API\Payment;
use Payment\Form\Config;
use System\Controller\BaseAbstractActionController;


class PaymentController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $pageType = true; // baraye inke dar hengame view bedanim az safhe digar amade ya na mikhahad mostaghim pardakht konad
        $showData = '';
        $mainData = '';
        $flagTransactions = false; //increase cash
        $data = $this->params()->fromQuery('routeParams');
        if ($data) {
            $data = base64_decode($data);
            $showData = unserialize($data);
            $showData['userMoney'] = 0;
            if (isset($showData['validate']['params']['transactions']) && $showData['validate']['params']['transactions'])
                $flagTransactions = true;
            if (isset($showData['amount']) && current_user()->id) {
                $finalPrice = $showData['amount'];
                $userMoney = getSM('transactions_api')->getTransactions(current_user()->id);
                $showData['userMoney'] = $userMoney;
                if ($userMoney > 0 && !$flagTransactions) {
                    if ($userMoney >= $showData['amount']) {
                        //set transactions
                        $dataTransactions = array(
                            'userId' => current_user()->id,
                            'note' => $showData['comment'],
                            'amount' => $showData['amount'] * -1,
                            'adminId' => current_user()->id,
                        );
                        $falgTransactions = getSM('transactions_api')->insertTransactions($dataTransactions);
                        //end
                        $showData['data'] = $showData;
                        $response = $this->redirectTo($showData);
                        if ($response !== false)
                            return $response;
                    }
                }
            }
            $mainData = $showData;
            if (!$flagTransactions) {
                $mainData['amount'] = $mainData['amount'] - $mainData['userMoney'];
                $mainData['validate']['params']['userMoney'] = $mainData['userMoney'];
            }
            $mainData['validate']['params']['userId'] = current_user()->id;
            unset($mainData['userMoney']);
            $mainData = serialize($mainData);
            $mainData = base64_encode($mainData);
            $pageType = false;
        } else {
            $pageType = true;
        }

        $bankName = getSM()->get('bank_info_table')->getBankName();

        Breadcrumb::AddMvcPage('Payment', 'app/payment');

        $this->viewModel->setTemplate('payment/payment/index');
        $this->viewModel->setVariables(array(
            'pageType' => $pageType,
            'showData' => $showData,
            'data' => $mainData,
            'bankName' => $bankName,
            'flagTransactions' => $flagTransactions
        ));
        return $this->viewModel;
    }

    public function sendPaymentAction()
    {
//            $form = new \Payment\Form\SendPay();
//            $form = prepareConfigForm($form);
//            $request = $this->getRequest();
//
//
//            if ($request->isPost()) {
//                $form->setData($request->getPost());
//                if ($form->isValid()) {
//                    $data = $form->getData();
//                    $mobile = $data['mobile'];
//                    $amount = $data['amount'];
//
//                    //$this->view->serverUrl() .
//                    $callback = $this->url(array('controller' => 'payment-return', 'action' => 'charge-jiring-account', 'bank' => 'mellat'));
//                    $payment = new \Payment\API\Mellat($callback);
//                    $result = $payment->InitClient(); // dakhele try bashad . be web servise bank vasl mishavad
//                    if (!$payment->ERROR) {
//                        $res = $payment->PAY_REQUEST($amount);
//                        if ($payment->ERROR)
//                            $this->viewModel->error = $res;
//                        else {
//                            $customer = new Application_Model_Customer();
//                            $order = new Application_Model_Order();
//                            $order->customerId = $customer->getCustomer($mobile);
//                            $order->paymentId = $payment->id;
//                            $order->productId = 1;
//                            $order->save();
//                            $this->forward()->dispatch('Payment\Controller\Payment', array('action' => 'ali', 'refId' => $payment->RefId, 'postUrl' => $payment->_postUrl));
//                        }
//
//                    } else
//                        $this->view->error = $result;
//
//                    //$form->reset();
//                }
//            }
//
//
//            return new ViewModel(array(
//                'form' => $form
//            ));
    }

    public function initializeAction()
    {
        $routeParams = array();
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            if (isset($data['bankName']) && $data['bankName']) {
                if (isset($data['routeParams']) && $data['routeParams']) {
                    $routeParams = base64_decode($data['routeParams']);
                    $routeParams = unserialize($routeParams);
                } elseif (isset($data['amount']) && $data['amount']) {
                    $routeParams = array(
                        'amount' => $data['amount'],
                        'email' => $data['email'],
                        'comment' => $data['comment'],
                    );
                }
            }

            IF (getCurrency() == 'IRT') //if system is based on Iran Toman
                //convert to Iran Rial
                $routeParams['amount'] = ((int)$routeParams['amount']) * 10;

            $paymentApi = $data['bankName'];
            $paymentApi = new $paymentApi($routeParams['amount']);

            $form = $paymentApi->init(array(
                'data' => $routeParams,
            ));
        }

        $this->viewModel->setTemplate('payment/payment/initialize');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function validateAction()
    {
        $trackingCode = 0;
        $result = '';
        $api = $this->params()->fromRoute('api', false);
        if (!$api)
            return $this->invalidRequest('app/front-page');

        $api = base64_decode($api);

        /* @var $api Payment */
        $api = new $api();

        $params = $this->params()->fromPost();
        $result = $api->validate($params);
        $paymentId = 0;
        if (is_array($result) && isset($result['status']) && $result['status'] === true) {

            //trigger reserve done event
            if (getSM()->has('points_api')) {
                getSM('points_api')->addPoint('Payment', 'Done', current_user()->id, t('online payment'));
                getSM('points_api')->addPoint('Payment', 'Amount', current_user()->id, t('online payment'), 1, $api->getPayedAmount());
            }

            $data = getSM('payment_table')->getData($result['id']);

            //set transactions
            if (isset($data['data']['validate']['userMoney']) && $data['data']['validate']['userMoney']) {
                $dataTransactions = array(
                    'userId' => $data['data']['validate']['userId'],
                    'note' => $data['comment'],
                    'amount' => $data['data']['validate']['userMoney'] * -1,
                    'adminId' => current_user()->id,
                );
                $falgTransactions = getSM('transactions_api')->insertTransactions($dataTransactions);
            }
            //end


            //TODO send mail

            $trackingCode = $result['trackingCode'];
            $paymentId = $result['id'];

            $message = array(
                'پرداخت اینترنتی شما با موفقیت انجام شد.',
                sprintf(t('PAYMENT_SUCCESS_ID'), $paymentId),
                sprintf(t('PAYMENT_SUCCESS_BANK_ID'), $trackingCode)
            );
            App::getSession()->offsetSet('payment_message', $message);

            $response = $this->redirectTo($data, $result['id']);
            if ($response !== false) {
                return $response;
            }
        }

        $this->viewModel->setTemplate('payment/payment/validate');
        return $this->viewModel->setVariables(array(
            'msg' => $result,
            'trackingCode' => $trackingCode,
            'paymentId' => $paymentId,
        ));
    }

    private function redirectTo($data = array(), $paymentId = 0)
    {
        if (isset($data['data']['validate'])) {
            if (isset($data['data']['validate']['route'])) {
                $params = array();
                if (isset($data['data']['validate']['params']))
                    $params = $data['data']['validate']['params'];

                //set entity
                $dataEntityPayment = array(
                    'paymentId' => $paymentId,
                    'entityId' => $params['id'],
                    'entityType' => $params['entityType'],
                    'userId' => $params['userId'],
                );
                $entityPaymentId = getSM('payment_entity_api')->save($dataEntityPayment);
                if (!$entityPaymentId)
                    $entityPaymentId = 0;
                //end
                $params = base64_encode(serialize($params));
                return $this->redirect()->toRoute($data['data']['validate']['route'], array('params' => $params, 'paymentId' => $paymentId));
            }
        }

//        return false;
    }

    public function myPaymentsAction()
    {
        $grid = new DataGrid('payment_table');
        $grid->route = 'admin/payment/my-payments';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $amount = new Column('amount', 'Price');
        $desc = new Column('comment', 'Description');

        $status = new Custom('status', 'Status', function (Column $col) {
            if ($col->dataRow->status == 0) {
                $data = unserialize($col->dataRow->data);
                $paymentParams = serialize($data['data']);
                $paymentParams = base64_encode($paymentParams);
                $url = url('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
                return '<a target="_blank" href="' . $url . '" rel="tooltip" title="' . t('Payment') . '" class="ui-button-icon-secondary ui-icon ui-icon-search" ></a>';
            }
        }, array('headerAttr' => array('width' => '34px'), 'attr' => array('align' => 'center')));

        $date = new Date('payDate', 'Date');

        $grid->addColumns(array($id, $amount, $desc, $date, $status));

        $this->viewModel->setTemplate('payment/payment/my-payments');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
        ));
        return $this->viewModel;

    }

}
