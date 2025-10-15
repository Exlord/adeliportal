<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/30/13
 * Time: 11:40 AM
 */

namespace OrgChart\Form\Config;


use Zend\Form\Fieldset;

class Config extends Fieldset
{
    public function __construct($fields)
    {
        parent::__construct('config');
        $this->setLabel('Fields');
        foreach ($fields as $id => $role) {
            $this->add(new Fields($id, $role));
        }
    }
}