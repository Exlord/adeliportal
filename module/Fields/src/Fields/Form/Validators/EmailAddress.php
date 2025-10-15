<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class EmailAddress extends BaseValidator
{
    protected $label = 'EmailAddress';
    protected $attributes = array(
        'id' => 'validator_EmailAddress',
        'name' => 'Zend\Validator\EmailAddress'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate an email address. The validator first splits the email address on local-part @ hostname and attempts to match these against known specifications for email addresses and hostnames.');
    }
}