<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Ip extends BaseValidator
{
    protected $label = 'Ip';
    protected $attributes = array(
        'id' => 'validator_Ip',
        'name' => 'Zend\Validator\Ip'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to validate if a given value is an IP address. It supports the IPv4, IPv6 and IPvFeature definitions.');
    }
} 