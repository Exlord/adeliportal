<?php
namespace NewsLetter\Form;

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
        parent::__construct('content_config');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'during',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'During',
                'description' => '',
            ),
            'attributes' => array(),
        ));

        $this->add(new \System\Form\Buttons('newsLetter_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();

    }
}
