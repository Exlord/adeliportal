<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class StringLength extends BaseValidator
{
    protected $label = 'StringLength';
    protected $attributes = array(
        'id' => 'validator_StringLength',
        'name' => 'Zend\Validator\StringLength'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('This validator allows you to validate if a given string is between a defined length.StringLength supports only string validation');

        $this->add(array(
            'name' => 'encoding',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Encoding',
                'description' => 'Sets the ICONV encoding which has to be used for this string.',
            ),
            'attributes' => array()
        ));
        $this->add(array(
            'name' => 'min',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Minimum',
                'description' => 'Sets the minimum allowed length for a string.',
            ),
            'attributes' => array(
                'class' => 'spinner',
            )
        ));
        $this->add(array(
            'name' => 'max',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Maximum',
                'description' => 'Sets the maximum allowed length for a string.',
            ),
            'attributes' => array(
                'class' => 'spinner',
            )
        ));
    }
} 