<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class GreaterThan extends BaseValidator
{
    protected $label = 'GreaterThan';
    protected $attributes = array(
        'id' => 'validator_GreaterThan',
        'name' => 'Zend\Validator\GreaterThan'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate if a given value is greater than a minimum border value.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'inclusive',
            'options' => array(
                'label' => 'inclusive',
                'description' => 'Defines if the validation is inclusive the minimum border value or exclusive. It defaults to FALSE.',
                'value_options' => array(
                    true => 'True',
                    false => 'False'
                )
            ),
            'attributes' => array(
                'class' => 'select2',
            ),
        ));

        $this->add(array(
            'name' => 'min',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Minimum',
                'description' => '',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 5,
            )
        ));
    }
} 