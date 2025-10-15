<?php

namespace NewsLetter\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;

class ConfigMore extends BaseForm
{
    private $apiName = array();

    public function __construct($apiName)
    {
        $this->apiName = $apiName;
        parent::__construct('newsLetter_config_more');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
        ));
    }

    protected function addElements()
    {

        $select = new \NewsLetter\Form\ConfigSelect(1,$this->apiName);
        $this->add($select);

        $this->add(new \System\Form\Buttons('newsLetter_config_more'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();

    }
}
