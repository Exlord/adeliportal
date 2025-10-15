<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Step extends BaseValidator
{
    protected $label = 'Step';
    protected $attributes = array(
        'id' => 'validator_Step',
        'name' => 'Zend\Validator\Step'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate if a given value is a valid step value. This validator requires the value to be a numeric value (either string, int or float).');

        $this->add(array(
            'name' => 'baseValue',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Base Value',
                'description' => 'This is the base value from which the step should be computed. This option defaults to 0',
            ),
            'attributes' => array(
                'class' => 'spinner',
            )
        ));

        $this->add(array(
            'name' => 'step',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Step',
                'description' => 'This is the step value. This option defaults to 1',
            ),
            'attributes' => array(
                'class' => 'spinner',
            )
        ));
    }
} 