<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/18/14
 * Time: 1:33 PM
 */
namespace Notify\Form;

use Notify\Form\Fieldsets\Modules;
use Notify\Form\Fieldsets\Roles;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Form\Fieldset;

class AdvanceConfig extends BaseForm
{

    public function __construct()
    {
        parent::__construct('notify_config');
        $this->setAction(url('admin/notify/config/advance'));
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    protected function addElements()
    {
        //notificationKey
        //|--send sms
        //|--|--template
        //|--send email
        //|--|--template
        //|--internal system notify
        //|--|--notify unread
        //|--|--|--with sms
        //|--|--|--|--template
        //|--|--|--with email
        //|--|--|--|--template

        $templates = getSM('template_table')->getArray();
        $list = getSM('notify_api')->loadBaseConfig();
        $this->add(new Roles($list, $templates));

        $this->add(new Buttons('notify_config'));
    }

    public function addInputFilters()
    {
        $filter = $this->getInputFilter();

        $roles = $filter->get('user_roles');
        foreach ($roles->getInputs() as $role) {
            $modules = $role->get('modules');

            foreach ($modules->getInputs() as $keys) {

                foreach ($keys->getInputs() as $types) {
                    foreach ($types->getInputs() as $input) {
                        if ($input->has('template'))
                            $input->get('template')->setRequired(false)->setAllowEmpty(true);
                    }
                }
            }
        }
    }
}