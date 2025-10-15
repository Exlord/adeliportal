<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/1/13
 * Time: 2:00 PM
 */

namespace User\Form;

use User\Form\Config\FieldsAccess;
use Zend\Form\Fieldset;

class Basic extends Fieldset
{
    private $password;

    public function __construct($password = true, $accessLevels = null)
    {
        $this->password = $password;
        parent::__construct('basic');

        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Username',
                'description' => 'Username should be between 5 to 25 characters. The characters are limited to English Alphabets and numbers, and these characters : _ - + ! @ # $ .',

            ),
            'attributes' => array(
                'class' => 'left-align',
                'style' => 'max-width:250px;'
            )
        ));

        if ($password) {
            $this->add(array(
                'name' => 'password',
                'type' => 'Zend\Form\Element\Password',
                'options' => array(
                    'label' => 'Password',
                    'description' => '',

                ),
                'attributes' => array(
                    'class' => 'left-align',
                    'style' => 'max-width:250px;'
                )
            ));
            $this->add(array(
                'name' => 'password_verify',
                'type' => 'Zend\Form\Element\Password',
                'options' => array(
                    'label' => 'Verify Password',

                ),
                'attributes' => array(
                    'class' => 'left-align',
                    'style' => 'max-width:250px;'
                )
            ));
        }
        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email',
                'description' => 'Email is required to reset accounts password if you ever forgot it.',

            ),
            'attributes' => array(
                'class' => 'left-align',
                'style' => 'max-width:250px;'
            )
        ));

        $this->add(array(
            'name' => 'displayName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Display Name',
                'description' => 'A User friendly name to display in place of Username',

            ),
            'attributes' => array(
                'style' => 'max-width:250px;'
            ),
        ));

        if ($accessLevels) {
            $data = new Fieldset('data');
            $data->add(new FieldsAccess($accessLevels));

            $this->add($data);
        }
    }
} 