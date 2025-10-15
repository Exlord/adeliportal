<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace GeographicalAreas\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class City extends Form
{
    public function __construct()
    {
        parent::__construct('city_form');

        $this->setAttribute('method', 'post')
            ->setAttribute('class', 'normal-form ajax_submit')
            ->setHydrator(new ClassMethodsHydrator(false))
            ->setInputFilter(new InputFilter());

        $this->add(array(
            'name' => 'cityTitle',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Title'
            ),
        ));

        $this->add(array(
            'name' => 'itemStatus',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Is Enabled ?'
            ),
        ));
        $this->add(array(
            'name' => 'itemOrder',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Order',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => -999,
                'data-max' => 999,
                'data-step' => 1,
            )
        ));
        $this->add(array(
            'name' => 'stateId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'State',
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));
        $this->add(array(
            'type' => 'System\Form\Buttons',
            'name' => 'buttons'
        ));
    }
}
