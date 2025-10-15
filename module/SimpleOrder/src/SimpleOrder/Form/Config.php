<?php

namespace SimpleOrder\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;

class Config extends BaseForm
{

    public function __construct()
    {
        parent::__construct('simple_order_config');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
            'action' => url('admin/simple-order/config'),
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(),
        ));

        $this->add(new Buttons('simple_order_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */
    }
}
