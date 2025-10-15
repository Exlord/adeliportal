<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Sms\Form;

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

class SendSms extends BaseForm
{
    public function __construct()
    {
        parent::__construct('sms_send');
        $this->setAttributes(array(
            'class'=> 'normal-form ajax_submit',
            'action'=> url('admin/sms/send-sms'),
        ));

    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'onemobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile'
            ),
        ));

        $text = new Element\Textarea('groupmobile');
        $text->setLabel("Group Mobile Number : Example 09140000000,09140000000");
        $text->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text);

        $text2 = new Element\Textarea('textsms');
        $text2->setLabel("Sms");
        $text2->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text2);

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-send',
            'attributes' => array(
                'value' => 'Send',
                'class' => 'button',
            ),

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
