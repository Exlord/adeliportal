<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/17/13
 * Time: 9:18 AM
 */

namespace Mail\Form;


use Mail\API\Mail;
use System\Form\BaseForm;
use System\Form\Buttons;

class Config extends BaseForm
{

    public function __construct()
    {
        parent::__construct('mail_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'emailFrom',
            'options' => array(
                'label' => 'Default Sender Email',
                'description' => 'System will use this email address when sending email to users if the sender is not specified',
            ),
            'attributes' => array(
                'value' => Mail::$DefaultSender,
                'class' => 'left-align',
                'size' => 60
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'emailFromName',
            'options' => array(
                'label' => 'Default Sender Email name',
                'description' => 'System will use this name when sending email to users if the sender name is not specified'
            ),
            'attributes' => array(
                'value' => Mail::$DefaultSenderName
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'emailTo',
            'options' => array(
                'label' => 'Default Recipient Email',
                'description' => 'System will use this email address when sending email to site admin',
            ),
            'attributes' => array(
                'value' => Mail::$DefaultRecipient,
                'class' => 'left-align',
                'size' => 60
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'emailToName',
            'options' => array(
                'label' => 'Default Recipient Email name',
                'description' => 'System will use this name when sending email to site admin'
            ),
            'attributes' => array(
                'value' => Mail::$DefaultRecipientName
            )
        ));
        $this->add(new Buttons('mail_config'));
    }

    protected function addInputFilters()
    {
        // TODO: Implement addInputFilters() method.
    }
}