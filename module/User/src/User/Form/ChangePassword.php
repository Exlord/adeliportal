<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/24/13
 * Time: 12:55 PM
 */

namespace User\Form;


use System\Form\BaseForm;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;

class ChangePassword extends BaseForm
{
    public function __construct()
    {
        $this->setAttribute('class', 'normal-form ajax_submit');
        parent::__construct('change_password');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'old_password',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Current Password',
                'description' => ''
            ),

        ));
        $this->add(array(
            'name' => 'new_password',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'New Password',
                'description' => ''
            ),

        ));
        $this->add(array(
            'name' => 'new_password_verify',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Verify New Password'
            ),

        ));

        $this->add(new \System\Form\Buttons('change_password'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $old_password \Zend\InputFilter\Input
         * @var $new_password \Zend\InputFilter\Input
         * @var $new_password_verify \Zend\InputFilter\Input
         */
        $filter = $this->getInputFilter();

        $old_password = $filter->get('old_password');
        $old_password
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 25)));
        $old_password->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags());

        $new_password = $filter->get('new_password');
        $new_password
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 25)));
        $new_password->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags());

        $new_password_verify = $filter->get('new_password_verify');
        $new_password_verify
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 25)))
            ->attach(new Identical(array('token' => 'new_password',
                'messages' => array(Identical::NOT_SAME => 'Password and Verify Password do not match'))));
        $new_password_verify->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags());
    }
}