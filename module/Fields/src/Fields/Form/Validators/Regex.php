<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Regex extends BaseValidator
{
    protected $label = 'Regex';
    protected $attributes = array(
        'id' => 'validator_Regex',
        'name' => 'Zend\Validator\Regex'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('This validator allows you to validate if a given string conforms a defined regular expression.');

        $this->add(array(
            'name' => 'pattern',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Pattern',
                'description' => '',
            ),
            'attributes' => array()
        ));
    }
} 