<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class LessThan extends BaseValidator
{
    protected $label = 'LessThan';
    protected $attributes = array(
        'id' => 'validator_LessThan',
        'name' => 'Zend\Validator\LessThan'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate if a given value is less than a maximum border value.LessThan supports only number validation');

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'inclusive',
            'options' => array(
                'label' => 'inclusive',
                'description' => 'Defines if the validation is inclusive the maximum border value or exclusive. It defaults to FALSE.',
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
            'name' => 'max',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Maximum',
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