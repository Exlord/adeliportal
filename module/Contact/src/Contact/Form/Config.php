<?php

namespace Contact\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;

class Config extends BaseForm
{
    private $allUser = array();

    public function __construct($allUser)
    {
        $this->allUser = $allUser;
        parent::__construct('contact_config');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
            'action' => url('admin/contact/config'),
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'defaultUser',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Default user',
                'empty_option' => '-- Select --',
                'value_options' => $this->allUser,
                'description' => "If the viewer does not select specific sections. By default, the information is sent to the upper user's email."
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'sendEmail',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'CONTACT_SEND_EMAIL_ADMIN',
                'description' => "CONTACT_SEND_EMAIL_ADMIN_DESC",
            ),
        ));

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


        $this->setRequiredFalse($filter, array(
            'defaultUser',
        ));

    }
}
