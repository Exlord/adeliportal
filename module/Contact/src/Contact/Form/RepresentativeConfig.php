<?php

namespace Contact\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;

class RepresentativeConfig extends BaseForm
{
    public function __construct()
    {
        parent::__construct('contact_representative_config');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
            'action' => url('admin/contact/representative-config'),
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'zoom',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Zoom'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'name' => 'center',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'CONTACT_MAP_CENTER_POSITION'
            ),
            'attributes' => array(),
        ));


        $this->add(new Buttons('contact_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();

    }
}
