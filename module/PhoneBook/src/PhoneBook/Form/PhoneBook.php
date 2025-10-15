<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace PhoneBook\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class PhoneBook extends BaseForm
{
    public function __construct()
    {
        parent::__construct('phone-book-form');
        $this->setAttribute('class', 'normal-form');
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));


        $this->add(array(
            'name' => 'nameAndFamily',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name'
            ),
        ));


        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
        ));


        $this->add(array(
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile'
            ),
        ));


        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Phone'
            ),
        ));


        $this->add(array(
            'name' => 'fax',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Fax'
            ),
        ));

        $this->add(array(
            'name' => 'comment',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Comment'
            ),
        ));

        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Hidden',
        ));


        $this->add(new \System\Form\Buttons('phone_book_form'));


    }


    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        $email = $filter->get('email');
        $email
            ->setAllowEmpty(false)
            ->setRequired(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)))
            ->attach(new EmailAddress());
        $email->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());
    }

}
