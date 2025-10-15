<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Int extends BaseValidator
{
    protected $label = 'Int';
    protected $attributes = array(
        'id' => 'validator_Int',
        'name' => 'Zend\I18n\Validator\Int'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('validates if a given value is an integer. Also localized integer values are recognised and can be validated.');
    }
} 