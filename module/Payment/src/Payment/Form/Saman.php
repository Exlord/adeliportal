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

class Saman extends BaseForm
{
    private $Amount;
    private $MID;
    private $ResNum;
    private $RedirectURL;

    public function __construct($Amount, $MID, $ResNum, $RedirectURL)
    {
        $this->Amount = $Amount;
        $this->MID = $MID;
        $this->ResNum = $ResNum;
        $this->RedirectURL = $RedirectURL;
        parent::__construct('saman');
        $this->setAttribute('class', 'normal-form');
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'Amount',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => $this->Amount
            )
        ));
        $this->add(array(
            'name' => 'MID',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => $this->MID
            )
        ));
        $this->add(array(
            'name' => 'ResNum',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => $this->ResNum
            )
        ));
        $this->add(array(
            'name' => 'RedirectURL',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => $this->RedirectURL
            )
        ));
        $this->add(array(
            'name' => 'btnPay',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Submit To Bank'
            )
        ));


    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();


    }
}
