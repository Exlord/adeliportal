<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/15/14
 * Time: 12:54 PM
 */

namespace FormsManager\Form;


use Zend\Form\Fieldset;

class FormConfig extends Fieldset
{
    public function __construct()
    {
        parent::__construct('config');
        $this->setLabel('Configs');

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'use_captcha',
            'options' => array(
                'label' => 'Add Captcha To Form',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'after_save_message',
            'options' => array(
                'label' => 'FORM_MANAGER_MESSAGE_AFTER_SAVE',
            ),
            'attributes' => array(
                'size' => '100'
            )
        ));
    }
} 