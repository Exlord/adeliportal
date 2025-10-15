<?php
namespace User\Form\Config;

use User\Model\UserTable;
use Zend\Form\Fieldset;

class UserStatus extends Fieldset
{
    private $roles;
    private $status;

    public function __construct($roles)
    {
        $this->roles = $roles;
        $this->status = UserTable::$accountStatus;
        $this->status[-1] = 'Inherited';
        parent::__construct('user_status');
        $this->setAttribute('id', 'user_status');
        $this->setLabel("New member's status");
        $this->setOptions(array('description' => "New members status for each user role. if 'Inherited' is selected the value from basic configuration will be used"));


        foreach ($this->roles as $val) {
            $this->add(array(
                'name' => $val->id,
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => $val->roleName,
                    'value_options' => $this->status,
                ),
                'attributes' => array(
                    'value' => '-1',
                    'class' => 'select2'
                )
            ));
        }
    }
}
