<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Digits extends BaseValidator
{
    protected $label = 'Digits';
    protected $attributes = array(
        'id' => 'validator_Digits',
        'name' => 'Zend\Validator\Digits'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('validates if a given value contains only digits.<br/>When you want to validate numbers or numeric values, be aware that this validator only validates digits. This means that any other sign like a thousand separator or a comma will not pass this validator. In this case you should use Zend\I18n\Validator\Int or Zend\I18n\Validator\Float.');
    }
} 