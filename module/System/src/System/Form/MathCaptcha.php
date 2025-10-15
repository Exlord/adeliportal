<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/19/14
 * Time: 9:51 AM
 */

namespace System\Form;


use Zend\Form\Fieldset;

class MathCaptcha extends Fieldset
{
    public function __construct()
    {
        parent::__construct('math');
        $this->setLabel('Math Captcha');

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'negative_result',
            'options' => array(
                'label' => 'Allow Negative Result',
                'description' => 'if checked the answers could have negative values otherwise they always will have positive values'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'negative_operators',
            'options' => array(
                'label' => 'Allow Negative Operators',
                'description' => 'if checked operators can have negative values otherwise min will be set to more that 0'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'min',
            'options' => array(
                'label' => 'Minimum',
                'description' => 'Minimum value for operators (default:0,min:-19,max:1)'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => -19,
                'data-max' => 1,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'max',
            'options' => array(
                'label' => 'Maximum',
                'description' => 'Maximum value for operators (default:9,min:9,max:19)'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 9,
                'data-max' => 19,
                'data-step' => 1,
            )
        ));
    }
} 