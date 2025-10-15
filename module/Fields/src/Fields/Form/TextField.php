<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class TextField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('text_field');
        $this->setAttribute('id', 'text_settings');
        $this->add(array(
            'name' => 'text_field_size',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Text Field Size',
                'description' => 'Field Size in Pixels',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 10,
                'data-max' => 150,
                'data-step' => 5,
                'value' => 50
            )
        ));

        $this->add(array(
            'name' => 'text_field_max_length',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Text Field Max Length',
                'description' => 'Max Number of allowed characters in the field'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-max' => 400,
                'data-step' => 1,
                'value' => 50
            )
        ));

        $this->add(array(
            'name' => 'text_field_watermark',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Watermark',
                'description' => 'This text will be displayed in the field if it is empty',
            ),
            'attributes' => array(
                'size' => 75
            )
        ));

        $this->add(array(
            'name' => 'text_type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Text Type',
                'value_options' => array(
                    'text' => 'Simple Text',
                    'text_email' => 'Email Address',
                    'text_web' => 'Web Site Address',
                    'integer' => 'Number',
                    'decimal' => 'Decimal',
                    'color' => 'Color'
                )
            ),
            'attributes' => array(
                'class' => 'select2',
                // 'id' => 'text_field_text_type'
            ),
        ));

        $this->add(array(
            'type' => 'Fields\Form\NumberField',
            'name' => 'number_field',
            'options' => array(
                'label' => 'Number Field Settings'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\DecimalField',
            'name' => 'decimal_field',
            'options' => array(
                'label' => 'Decimal Field Settings'
            )
        ));
    }
}
