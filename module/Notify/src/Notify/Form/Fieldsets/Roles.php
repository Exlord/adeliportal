<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/23/14
 * Time: 11:27 AM
 */

namespace Notify\Form\Fieldsets;


use Zend\Form\Fieldset;

class Roles extends Fieldset
{
    public function __construct($list, $templates)
    {
        parent::__construct('user_roles');
        $this->setLabel('User Roles Notify Settings');
        $this->setOptions(array('description' => 'global notification settings for all modules per user role'));

        $roles = getSM('role_table')->getVisibleRoles();
        foreach ($roles as $role) {
            $this->add(new Role($role, $list, $templates));
        }
    }
} 