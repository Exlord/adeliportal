<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/23/14
 * Time: 11:06 AM
 */

namespace Notify\Form\Fieldsets;


use Zend\Form\Fieldset;

class Module extends Fieldset
{
    public function __construct($module, $keys, $templates, $perUserRoles = false)
    {
        parent::__construct($module);
        $this->setLabel($module);
        foreach ($keys as $nKey => $params) {
            if ($perUserRoles)
                if (isset($params['allow_user_role_override']) && $params['allow_user_role_override'] == false)
                    continue;

            if (isset($params['notify_with'])) {
                $this->add(new Event($nKey, $params, $templates, $perUserRoles));
            }
        }
    }
} 