<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class NumberField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('text_field');
        $this->setAttribute('id', 'integer_settings');
        $this->add(array(
            'name' => 'number_field_max',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Max',
                'description' => 'The Biggest number that this field can have',
            ),
            'attributes' => array(
                'class' => 'spinner big_spinner',
                'data-step' => 10,
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
                'class' => 'spinner big_spinner',
                'data-step' => 10,
                'value' => 0,

            )
        ));
    }
}
