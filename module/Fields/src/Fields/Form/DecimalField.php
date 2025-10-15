<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class DecimalField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('text_field');
        $this->setAttribute('id', 'decimal_settings');

        $this->add(array(
            'name' => 'decimal_field_precision',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Decimal Precision',
                'description' => 'The number of digits after the decimal point',
            ),
            'attributes' => array(
                'class' => 'spinner big_spinner',
                'data-min' => 1,
                'data-max' => 10,
                'data-step' => 1,
                'value' => 1
            )
        ));

        $this->add(array(
            'name' => 'number_field_max',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Max',
                'description' => 'The Biggest number that this field can have',
            ),
            'attributes' => array(
                'class' => 'spinner big_spinner decimal',
                'data-step' => 0.5,
                'value' => 0
            )
        ));

        $this->add(array(
            'name' => 'number_field_min',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Min',
                'description' => 'The smallest number that this field can have'
            ),
            'attributes' => array(
                'class' => 'spinner big_spinner decimal',
                'data-step' => 0.5,
                'value' => 0,

            )
        ));
    }
}
