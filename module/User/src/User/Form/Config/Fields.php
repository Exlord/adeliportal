<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/30/13
 * Time: 11:40 AM
 */

namespace User\Form\Config;


use Zend\Form\Fieldset;

class Fields extends Fieldset
{
    public function __construct($id, $role, $fields)
    {
        parent::__construct($id);
        $this->setLabel($role->roleName);
        $this->setAttribute('data-id', $id);

        foreach ($fields as $key => $value) {
            $this->add(array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => $key,
                'options' => array(
                    'label' => $value,
                ),
                'attributes' => array(
                    'data-id' => $key
                )
            ));
        }

    }
} 