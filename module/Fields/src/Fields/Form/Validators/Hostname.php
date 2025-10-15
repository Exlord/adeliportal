<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Hostname extends BaseValidator
{
    protected $label = 'Hostname';
    protected $attributes = array(
        'id' => 'validator_Hostname',
        'name' => 'Zend\Validator\Hostname'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate a hostname against a set of known specifications.');
    }
} 