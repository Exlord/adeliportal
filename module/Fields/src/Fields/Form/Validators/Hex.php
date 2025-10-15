<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Hex extends BaseValidator
{
    protected $label = 'Hex';
    protected $attributes = array(
        'id' => 'validator_Hex',
        'name' => 'Zend\Validator\Hex'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate if a given value contains only hexadecimal characters. These are all characters from 0 to 9 and A to F case insensitive. There is no length limitation for the input you want to validate.');
    }
} 