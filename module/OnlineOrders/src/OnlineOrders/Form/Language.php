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
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class Language extends BaseForm
{
    public function __construct()
    {
        parent::__construct('Language_form');
        $this->setAttributes(array(
            'class', 'normal-form',
            'action'=>url('admin/online-orders/language-select')
        ));
    }
    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));


        $this->add(array(
            'name' => 'langName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Language Name : '
            ),
        ));


        $this->add(array(
            'name' => 'langCode',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Language Code : '
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_Language_form'
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
