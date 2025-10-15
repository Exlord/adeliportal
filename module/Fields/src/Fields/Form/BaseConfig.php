<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 10:17 AM
 */

namespace Fields\Form;


use Zend\Form\Fieldset;

class BaseConfig extends Fieldset{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('class', 'collapsible collapsed');
    }
} 