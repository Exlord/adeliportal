<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/17/13
 * Time: 9:18 AM
 */

namespace FormsManager\Form;


use System\Form\BaseForm;
use System\Form\Buttons;

class Config extends BaseForm
{

    public function __construct()
    {
        parent::__construct('forms_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'emailFrom',
            'options' => array(
                'label' => 'Sender Email',
                'description' => 'System will use this email address when sending email to users'
            ),
            'attributes' => array(
                'class' => 'left-align',
                'size' => 60
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'emailFromName',
            'options' => array(
                'label' => 'Sender Email name',
                'description' => 'System will use this name when sending email to users'
            )
        ));
        $this->add(new Buttons('forms_config'));
    }

    protected function addInputFilters()
    {
        // TODO: Implement addInputFilters() method.
    }
}