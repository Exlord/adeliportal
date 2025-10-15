<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace User\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\Validator\StringLength;

class Login extends BaseForm
{
    public function __construct($redirect = false)
    {
        $options = array();
        if ($redirect)
            $options['query']['redirect'] = $redirect;
        $this->setAction(url('app/user/login', array(), $options));
        $this->setAttribute('class', 'normal-form');
        parent::__construct('login');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
//                'label' => 'Username',
                'add-on-prepend' => t('Username')
            ),
            'attributes' => array(
                'data-toggle' => 'tooltip',
                'data-placement' => 'right',
                'title' => t('Enter your username here')
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
//                'label' => 'Password',
                'add-on-prepend' => t('Password')
            ),
            'attributes' => array(
                'data-toggle' => 'tooltip',
                'data-placement' => 'right',
                'title' => t('Enter your password here')
            ),
        ));

        // $this->add(array(
        //     'type' => 'Zend\Form\Element\Csrf',
        //     'name' => 'csrf__login'
        // ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Button',
            'options' => array(
                'label' => 'Login',
                'glyphicon' => 'log-in'
            ),
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Login',
                'class' => 'btn btn-default',
                'id' => 'submit'
            )
        ));
    }

    protected function addInputFilters()
    {
        /**
         * @var $username Input
         */

        $filter = $this->getInputFilter();
        $username = $filter->get('username');
        $username->setRequired(true)
            ->setAllowEmpty(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 25)));

        $username
            ->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags());

        $password = $filter->get('password');
        $username = $filter->get('username');
        $username
            ->setRequired(true)
            ->setAllowEmpty(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 25)));
        $username
            ->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags());
    }
}
