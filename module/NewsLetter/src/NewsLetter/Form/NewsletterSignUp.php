<?php

namespace NewsLetter\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class NewsletterSignUp extends BaseForm
{
    private $categoryArray = array();

    public function __construct($categoryArray)
    {
        $this->categoryArray = $categoryArray;
        parent::__construct('newsletter_sign_up');
        $this->setAttributes(array(
            'method' => 'post',
            'action' => url('app/newsletter-sign-up')
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'multiple' => 'multiple',
                'size' => 10,
                'class' => 'select2',
            ),
            'name' => 'config',
            'options' => array(
                'disable_inarray_validator' => true,
                'label' => 'Tags',
                'value_options' => $this->categoryArray,
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(
                'placeholder' => t('Email'),
                'class' => 'form-text'
            )
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-save',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'button',
            )
        ));


    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();
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
    }
}