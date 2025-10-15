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

class Transactions extends BaseForm
{
    public function __construct()
    {
        parent::__construct('transactions');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel', url('admin/users'));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                    '1' => 'Increase',
                    '2' => 'Decrease',
                )
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'userId',
            'type' => 'Zend\Form\Element\Hidden',
            'options' => array(),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'amount',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Amount',
            ),
            'attributes' => array(
               // 'disabled' => 'disabled'
            )
        ));


        $this->add(array(
            'name' => 'note',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Note'
            ),
        ));


        $this->add(new \System\Form\Buttons('transactions'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();


    }
}
