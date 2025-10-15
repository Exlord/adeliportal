<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/18/14
 * Time: 1:33 PM
 */
namespace Notify\Form;

use Notify\Form\Fieldsets\Modules;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Form\Fieldset;

class Config extends BaseForm
{

    public function __construct()
    {
        parent::__construct('notify_config');
        $this->setAction(url('admin/notify/config'));
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    protected function addElements()
    {
        $users = getSM()->get('user_table')->getByRoleId(4);
        $this->add(array(
            'type' => 'Select',
            'name' => 'systemNotificationRecipient',
            'options' => array(
                'label' => 'System Notification Recipient',
                'description' => 'the selected user will receive all notifications intended for site admin',
                'value_options' => $users
            ),
            'attributes' => array(
                'placeholder' => 'Educator',
                'id' => 'educator',
                'class' => 'select2'
            )
        ));

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

        $modules = new Modules($list, $templates);
        $modules->setOptions(array('description' => 'global notification settings for all modules and all user roles and all users'));

        $this->add($modules);
        $this->add(new Buttons('notify_config'));
    }

    public function addInputFilters()
    {
        $filter = $this->getInputFilter();

        $modules = $filter->get('modules');

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