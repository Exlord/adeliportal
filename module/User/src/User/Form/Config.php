<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace User\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use User\Form\Config\Fields;
use User\Form\Config\FieldsAccess;
use User\Form\Config\Roles;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;

class Config extends BaseForm
{
    private $singUpRoles;
    private $fieldsRoles;
    private $fields;
    private $loginRoles;
    private $template = array();

//    protected $loadInputFilters = false;

    public function __construct($roles, $nestedRoles, $fields_list)
    {
        $this->singUpRoles = $nestedRoles;
        unset($this->singUpRoles[1]);

        $this->loginRoles = $nestedRoles;
        unset($this->loginRoles[1]);
        $this->loginRoles = array(0 => t('Everybody')) + $this->loginRoles;

        $this->fieldsRoles = $roles;
        unset($this->fieldsRoles[1]);

        $this->fields = $fields_list;

//        $this->template = $template;

        parent::__construct('user_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('action', url('admin/users/config'));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'allow_register',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'New User Registration',
                'description' => 'Should users be be able to signup for a new account ?',
            ),
        ));

        $this->add(array(
            'name' => 'general_details_in_register_form',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'General details in register form',
                'description' => 'Display and Request the user to fill out general details about themselves when registering for a new account',
            ),
        ));

//        $this->add(array(
//            'name' => 'allow_send_sms_pass',
//            'type' => 'Zend\Form\Element\Checkbox',
//            'options' => array(
//                'label' => 'Send sms to user for show password ?',
//                'description' => 'In recovery password section .',
//            ),
//        ));

        $this->add(array(
            'name' => 'new_user_unapproved',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => "New member's need admin approval",
                'description' => 'if this option is checked admin needs to approve new members before they can use their account',
            ),
        ));

        $this->add(array(
            'name' => 'userRegRole',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'User roles for new users ?',
                'value_options' => $this->singUpRoles,
                'description' => 'If the user is not registered and automated registration is done. New users can register on the user role.',
            ),
            'attributes' => array(
                'class' => 'select2'
            ),

        ));

        $this->add(array(
            'name' => 'limit_login',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Limited Login',
                'value_options' => $this->loginRoles,
                'description' => 'Limit login for only the members of selected user roles',
            ),
            'attributes' => array(
                'class' => 'select2',
                'multiple' => 'multiple'
            )
        ));

        $this->add(array(
            'name' => 'register_roles',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'User Roles',
                'description' => 'Choose the roles that new users can select when signing up',
                'value_options' => $this->singUpRoles
            ),
            'attributes' => array(
                'class' => 'select2',
                'multiple' => 'multiple'
            )
        ));

//        $emailTemplates = new Config\EmailTemplates($this->template);
//        $emailTemplates->setOptions(array('description' => 'Global settings for all user roles'));
//        $this->add($emailTemplates);

//        $sms = new Config\SmSTemplates($this->template);
//        $sms->setOptions(array('description' => 'Global settings for all user roles'));
//        $this->add($sms);

        $this->add(new FieldsAccess($this->fields));
        $this->add(new Roles($this->fieldsRoles, $this->fields));
        $this->add(new Buttons('user_config'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();
        $register_roles = $filter->get('register_roles');
        $register_roles->setRequired(false);

        foreach ($filter->get('roleType_fields')->getInputs() as $inputs) {
            foreach ($inputs->getInputs() as $input)
                $input->setRequired(false);
        }

//        foreach ($filter->get('email_templates')->getInputs() as $input) {
//            $input->setRequired(false);
//        }
//        foreach ($filter->get('sms')->getInputs() as $input) {
//            $input->setRequired(false);
//        }

    }
}
