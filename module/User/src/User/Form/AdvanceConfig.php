<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/30/13
 * Time: 1:20 PM
 */

namespace User\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use User\Form\Config\Roles;
use User\Form\Config\Templates;
use Zend\Form\Fieldset;

class AdvanceConfig extends BaseForm
{
    private $fieldsRoles;
    private $userRoles;
    private $templates;

    public function __construct($roles, $userRoles = array())
    {
        $this->fieldsRoles = $roles;
        $this->userRoles = $userRoles;
        unset($this->userRoles[1]);
//        $this->templates = $templates;
        parent::__construct('advance_user_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAction(url('admin/users/config/more'));
    }

    protected function addElements()
    {
        $roles = new Roles($this->fieldsRoles);
        $roles->setLabel('User Register Fields');
        $roles->setOptions(array('description' => 'Select user profile fields for each user role type that should be available in user register form'));
        $this->add($roles);

        $this->add(new Config\UserStatus($this->userRoles));
//        $this->add(new Templates($this->userRoles, $this->templates));

        $this->add(new Buttons('advance_user_config'));
    }
}