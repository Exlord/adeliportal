<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Alpha extends BaseValidator
{
    protected $label = 'Alpha';
    protected $attributes = array(
        'id' => 'validator_Alpha',
        'name' => 'Zend\I18n\Validator\Alpha'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate if a given value contains only alphabetical characters. There is no length limitation for the input you want to validate. This validator is related to the Zend\I18n\Validator\Alnum validator with the exception that it does not accept digits.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'allowWhiteSpace',
            'options' => array(
                'label' => 'Allow WhiteSpace',
                'description' => 'If set to true then whitespace characters are allowed. Otherwise they are suppressed. Default is “false” (whitespace is not allowed).'
            )
        ));
    }
} 