<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Payment\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class SendPay extends BaseForm
{
    public function __construct()
    {
        parent::__construct('payment_sendpayment');
        $this->setAttribute('class', 'normal-form');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'your mobile number : '
            ),
        ));
        $this->add(array(
            'name' => 'amount',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Amount : '
            ),
        ));


        $this->add(new \System\Form\Buttons('payment_sendpayment'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();


    }
}
