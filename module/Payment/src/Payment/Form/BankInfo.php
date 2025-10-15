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

class BankInfo extends BaseForm
{
    public function __construct()
    {
        parent::__construct('bank_info');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel', url('admin/payment/bank-info'));
    }

    protected function addElements()
    {
        /* $selc = new Element\Select('name');
         $selc->setLabel('whats name your bank ? ');
         $selc->setValueOptions(array(
             'saman' => 'saman',
             'mellat' => 'mellat',
         ));*/
        // $this->add($selc);

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Bank Name',
            ),
            'attributes' => array(
                'disabled' => 'disabled'
            )
        ));


        $this->add(array(
            'name' => 'userName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'User Name'
            ),
        ));
        $this->add(array(
            'name' => 'passWord',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Password'
            ),
        ));

        $this->add(array(
            'name' => 'terminalId',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Terminal Id'
            ),
        ));

        $this->add(new \System\Form\Buttons('bank_info'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();


    }
}
