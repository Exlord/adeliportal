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

class Config extends BaseForm
{
    public function __construct()
    {
        parent::__construct('onlineOrders_config');
        $this->setAttributes(array(
            'class', 'normal-form',
            'action'=>url('admin/online-orders/config')
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'langPercent',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'language Percent : '
            ),
        ));


        $this->add(array(
            'name' => 'supportPercent',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'support Percent : '
            ),
        ));

        $text = new Element\Textarea('txtSms');
        $text->setLabel("Text Sms : ");
        $text->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text);

        $this->add(new \System\Form\Buttons('onlineOrders_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();


    }
}
