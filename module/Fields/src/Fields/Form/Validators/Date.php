<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Date extends BaseValidator
{
    protected $label = 'Date';
    protected $attributes = array(
        'id' => 'validator_Date',
        'name' => 'Zend\Validator\Date'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate if a given value contains a date.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'format',
            'options' => array(
                'label' => 'Format',
                'description' => 'Sets the format which is used to write the date.'
            )
        ));
    }
} 