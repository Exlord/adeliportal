<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace SimpleOrder\Form;

use System\Captcha\CaptchaFactory;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class SimpleOrder extends BaseForm
{
    public function __construct()
    {
        parent::__construct('simple_order');
        $this->setAttributes(array(
            'class' => 'normal-form simple-order-form',
            'action' => url('app/simple-order'),
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name'
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
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Description'
            ),
        ));

        $categoryCollection = new \SimpleOrder\Form\CategoryCollection();
        $this->add($categoryCollection);


        $this->add(CaptchaFactory::create());


        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_simple_order_form'
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Send',
                'class' => 'button',
            )
        ));

    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();

        $this->filterByDigit($filter, array(
            'mobile',
        ));

        $this->filterByTrimAndTags($filter, array(
            'mobile',
            'email',
            'description',
        ));

        $ownerName = $filter->get('name');
        $ownerName->setRequired(true);
        $ownerName->getFilterChain()
            ->attach(new \Zend\Filter\StringTrim())
            ->attach(new \Zend\Filter\StripTags());
        $ownerName->getValidatorChain()
            ->attach(new \Zend\Validator\StringLength(array('max' => 200)))
            ->attach(new \Zend\Validator\NotEmpty());

        $ownerName = $filter->get('mobile');
        $ownerName->setRequired(true);
        $ownerName->getValidatorChain()
            ->attach(new \Zend\Validator\StringLength(array('max' => 11)))
            ->attach(new \Zend\Validator\NotEmpty());


        $email =  $filter->get('email');
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
