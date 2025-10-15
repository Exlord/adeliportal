<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/23/14
 * Time: 11:23 AM
 */

namespace Notify\Form\Fieldsets;


use Zend\Form\Fieldset;

class NotifyWith extends Fieldset
{
    public function __construct($nT, $template, $templates, $inherited = false)
    {
        parent::__construct($nT);

        $sendLabel = 'Send';
        switch ($nT) {
            case 'sms':
                $sendLabel = 'Send SmS';
                break;
            case 'email':
                $sendLabel = 'Send Email';
                break;
            case 'internal':
                $sendLabel = 'Send Internal Notification';
                break;
        }
        if (!$inherited) {
            $this->add(array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'send',
                'options' => array(
                    'label' => $sendLabel
                )
            ));
        } else {
            $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'send',
                'options' => array(
                    'label' => $sendLabel,
                    'value_options' => array(
                        '2' => 'Default Setting',
                        '0' => 'No',
                        '1' => 'Yes'
                    )
                ),
                'attributes'=>array(
                    'class'=>'select2',
                )
            ));
        }

        if ($nT != 'internal') {
            $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'template',
                'options' => array(
                    'label' => 'Template',
                    'empty_option' => 'Default Template',
                    'value_options' => $templates
                ),
                'attributes'=>array(
                    'class'=>'select2',
                )
            ));
        }
    }
} 