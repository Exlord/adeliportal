<?php

namespace Contact\Form;

use Application\API\App;
use System\Captcha\CaptchaFactory;
use System\Filter\FilterHtml;
use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class Contact extends BaseForm
{
    private $selectValue = null;
    private $sendId = 0;

    public function __construct($sendId, $selectValue = null)
    {
        $this->selectValue = $selectValue;
        $this->sendId = $sendId;

        parent::__construct('contact_form');
        $this->setAttributes(array(
            'class' => 'normal-form',
            'action' => url('app/contact/single', array('contactId' => $this->sendId))
        ));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'sendIds',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => $this->sendId,
            )
        ));


        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name'
            ),
            'attributes' => array(
                'placeholder' => t('Name'),
                'class' => ''
            )
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(
                'placeholder' => t('Email'),
                'class' => 'text-left'
            )
        ));

        $this->add(array(
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile'
            ),
            'attributes' => array(
                'placeholder' => t('Mobile'),
                'class' => 'text-left'
            )
        ));


        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Description'
            ),
            'attributes' => array(
                'placeholder' => t('Description'),
                'class' => 'form-textarea',
                'rows' => 5
            )
        ));

        if (is_array($this->selectValue)) {
            $this->add(array(
                'name' => 'typeContact',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Type',
                    'value_options' => $this->selectValue,
                ),
                'attributes' => array(
                    'class' => 'form-select'
                )
            ));
        }


        $this->add(CaptchaFactory::create());

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-send',
            'attributes' => array(
                'value' => 'Send',
                'class' => 'btn btn-default btn-lg btn-block',
            )
        ));

    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        /*$this->setRequiredFalse($filter, array(
            'mobile',
            'description'
        ));*/

        $mobile = $filter->get('mobile');
        $mobile
            ->setAllowEmpty(false)
            ->setRequired(true);

        $description = $filter->get('description');
        $description
            ->setAllowEmpty(false)
            ->setRequired(true);

        $name = $filter->get('name');
        $name
            ->setAllowEmpty(false)
            ->setRequired(true);

        $this->filterByTrimAndTags($filter, array(
            'name',
        ));

        $this->filterByDigit($filter, array(
            'mobile',
        ));

        $email = $filter->get('email');
        $email
            ->setAllowEmpty(true)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)))
            ->attach(new EmailAddress());
        $email->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());

        // $filter->get('tags')->setAllowEmpty(true);
        // $filter->get('tags')->setRequired(false);

        $description = $filter->get('description');
        $description->getFilterChain()->attach(new FilterHtml())->attach(new Filter\StringTrim());
    }
}
