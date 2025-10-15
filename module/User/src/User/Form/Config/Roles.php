<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/30/13
 * Time: 11:40 AM
 */

namespace User\Form\Config;


use Zend\Form\Fieldset;

class Roles extends Fieldset
{
    public function __construct($roles, $fields = false)
    {
        parent::__construct('roleType_fields');
        $this->setLabel('User Profile Fields');
        $this->setOptions(array('description' => 'Select user profile fields for each user role type. Create new fields throw Fields Module for User Profile'));

        foreach ($roles as $id => $role) {
            $fields_list = null;
            if (!$fields) {
                if (isset($role->fields))
                    $fields_list = $role->fields;
            } else
                $fields_list = $fields;

            if (is_array($fields_list))
                $this->add(new Fields($id, $role, $fields_list));
        }
    }
}