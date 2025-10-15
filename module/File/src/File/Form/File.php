<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace File\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class File extends Form
{
    public function __construct()
    {
        parent::__construct('file');

        $this->setAttribute('method', 'post')
            ->setAttribute('class', 'normal-form')
            ->setHydrator(new ClassMethodsHydrator(false))
            ->setInputFilter(new InputFilter());

        $this->add(array(
            'name' => 'roleName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Role Name : '
            ),
            'attributes' => array(
                'required' => 'required'
            ),
            'required' => true,
            'validators' => array(
                array('name' => 'not_empty',),
                array('name' => 'string_length', 'options' => array('min' => 5, 'max' => 200),),
            ),
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));

        $this->add(array(
            'type' => 'System\Form\Buttons',
            'name' => 'buttons'
        ));
    }
}
