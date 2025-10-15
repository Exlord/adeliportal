<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;


use Zend\Form\Fieldset;

class BaseValidator extends Fieldset
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('class', 'collapsible collapsed');
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'apply',
            'options' => array(
                'label' => 'Apply this validator',
                'description' => ''
            )
        ));
    }

    public function setDescription($description)
    {
        $this->setOptions(array('description' => $description));
    }
} 