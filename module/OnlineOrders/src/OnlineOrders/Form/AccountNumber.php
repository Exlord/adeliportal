<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OnlineOrders\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;

class AccountNumber extends BaseForm
{


    public function __construct()
    {

        parent::__construct('account_number_form');
        $this->setAttributes(array(
            'class', 'normal-form',
            'action'=>url('admin/online-orders/account-number')
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'ID',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'bankName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Bank Name'
            ),
        ));

        $this->add(array(
            'name' => 'nameAndFamily',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name And Family'
            ),
        ));

        $this->add(array(
            'name' => 'cardNumber',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Card Number'
            ),
        ));

        $this->add(array(
            'name' => 'accountNumber',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Account Number'
            ),
        ));


        $this->add(array(
            'name' => 'shebaNumber',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Sheba Number'
            ),
        ));


        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Do you want to be displayed on the site?'
            ),
        ));



        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_account_number_form'
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-create',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'button',
            )
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-edit',
            'attributes' => array(
                'value' => 'Edit',
                'class' => 'button',
            )
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-delete',
            'attributes' => array(
                'value' => 'Delete',
                'class' => 'button',
            )
        ));



    }


    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();

        //    $sampleFiled = $filter->get('sampleField');
    }

}
