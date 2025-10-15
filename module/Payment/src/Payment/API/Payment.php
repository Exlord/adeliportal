<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/17/13
 * Time: 11:50 AM
 */

namespace Payment\API;


use Application\API\App;
use Zend\View\Model\ViewModel;

abstract class Payment
{
    protected $model;
    protected $redirect;
    /**
     * @var \Zend\Soap\Client
     */
    protected $client;
    protected $payedAmount = 0;

    public function __construct($amount)
    {
        $this->model = new \Payment\Model\Payment();
        $this->model->payDate = time();
        $this->model->userId = current_user()->id;
        $this->model->amount = $amount;
        $api = base64_encode(get_called_class());
        $this->redirect = App::siteUrl() . url('app/payment/validate', array('api' => $api)); // back from bank
    }

    abstract public function init($data = array());

    abstract public function validate($params = array());

    final protected function renderForm($view, $params = array())
    {
        $viewModel = new ViewModel($params);
        $viewModel->setTemplate($view);
        $viewModel->setTerminal(true);
        return getSM('viewrenderer')
            ->render($viewModel);
    }

    final protected function save()
    {
        $this->model->data = serialize($this->model->data);
        return getSM('payment_table')->save($this->model);
    }

    public function getPayedAmount()
    {
        return $this->payedAmount;
    }
}