<?php

namespace ContentSharing\Form;

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
        parent::__construct('content_sharing_config');
        $this->setAttributes(array(
            'class'=> 'normal-form ajax_submit',
            'action'=> url('admin/content-sharing/config')
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'visibleStatus',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Show  at the page ?',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'Yes'
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(new \System\Form\Buttons('content_sharing_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();


    }
}
