<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class LongTextField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('long_text_field');
        $this->setAttribute('id', 'long_text_settings');
        $this->add(array(
            'name' => 'long_text_maxLength',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Max Length',
                'description' => 'Max Number of allowed characters in the field',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 10,
                'data-max' => 2000,
                'data-step' => 10,
                'value' => 100
            )
        ));

        $this->add(array(
            'name' => 'long_text_cols',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Columns',
                'description' => 'The TextAreas number of columns',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 5,
                'data-max' => 100,
                'data-step' => 1,
                'value' => 5
            )
        ));
        $this->add(array(
            'name' => 'long_text_rows',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Rows',
                'description' => 'The TextAreas number of rows',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 2,
                'data-max' => 10,
                'data-step' => 1,
                'value' => 2
            )
        ));
    }
}
