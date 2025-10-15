<?php

namespace Fields\Form\Validators;

class Between extends BaseValidator
{
    protected $label = 'Between';
    protected $attributes = array(
        'id' => 'validator_Between',
        'name' => 'Zend\Validator\Between'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate if a given value is between two other values.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'inclusive',
            'options' => array(
                'label' => 'inclusive',
                'description' => 'Defines if the validation is inclusive the minimum and maximum border values or exclusive.',
                'value_options' => array(
                    true => 'True',
                    false => 'False'
                )
            ),
            'attributes'=>array(
                'class'=>'select2',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'max',
            'options' => array(
                'label' => 'max',
                'description' => 'Sets the maximum border for the validation.',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'min',
            'options' => array(
                'label' => 'min',
                'description' => 'Sets the minimum border for the validation.',
            )
        ));

    }
} 