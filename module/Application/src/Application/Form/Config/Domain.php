<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 4/20/14
 * Time: 12:59 PM
 */

namespace Application\Form\Config;


use Zend\Form\Fieldset;

class Domain extends Fieldset
{
    public function __construct($domain)
    {
        parent::__construct($domain);
        $this->setLabel($domain);
        $this->add(array(
            'name' => 'default_route',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Default Route',
            ),
            'attributes' => array(
                'value' => '/',
                'dir' => 'ltr',
                'class' => 'left-align',
                'style' => 'max-width:300px;'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'intro',
            'options' => array(
                'label' => 'Application_Config_HasIntroPage',
                'description' => 'a standalone intro view script will be rendered as the sites first page'
            ),
        ));

    }
} 