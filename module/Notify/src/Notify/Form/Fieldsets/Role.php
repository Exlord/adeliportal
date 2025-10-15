<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/23/14
 * Time: 11:38 AM
 */

namespace Notify\Form\Fieldsets;


use Zend\Form\Fieldset;

class Role extends Fieldset
{
    public function __construct($role, $list, $templates)
    {
        parent::__construct($role->id);
        $this->setLabel($role->roleName);
        $this->setOptions(array('indent' => $role->indent));

        $this->add(new Modules($list, $templates, true));
    }
} 