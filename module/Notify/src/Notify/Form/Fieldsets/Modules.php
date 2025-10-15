<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/23/14
 * Time: 11:06 AM
 */

namespace Notify\Form\Fieldsets;


use Zend\Form\Fieldset;

class Modules extends Fieldset
{
    public function __construct($list, $templates, $perUserRoles = false)
    {
        parent::__construct('modules');
        $this->setLabel('Modules Notification Settings');

        foreach ($list as $module => $keys) {
            $this->add(new Module($module, $keys, $templates, $perUserRoles));
        }
    }
} 