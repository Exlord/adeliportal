<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Float extends BaseValidator
{
    protected $label = 'Float';
    protected $attributes = array(
        'id' => 'validator_Float',
        'name' => 'Zend\I18n\Validator\Float'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate if a given value contains a floating-point value. This validator validates also localized input.');
    }
} 