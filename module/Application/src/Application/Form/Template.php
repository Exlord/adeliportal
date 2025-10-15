<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/24/13
 * Time: 11:02 AM
 */

namespace Application\Form;


use System\Form\BaseForm;
use System\Form\Buttons;

class Template extends BaseForm
{

    public function __construct()
    {
        $this->setAttribute('class', 'ajax_submit');
        $this->setAttribute('data-cancel', url('admin/template'));
        parent::__construct('application_template');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'title',
            'options' => array(
                'label' => 'Title'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'format',
            'options' => array(
                'label' => 'Format',
                'description' => 'The KEYWORDS provided below will be replaced with their values.'
            ),
            'attributes' => array(
                'cols' => 50,
                'rows' => 5
            )
        ));

        $buttons = new Buttons('email_template');
        $buttons->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'toggle-editor',
            'attributes' => array(
                'type' => 'button',
                'class' => 'btn btn-primary',
                'id' => 'toggle-editor'
            ),
            'options' => array(
                'label' => 'Toggle Editor',
                'glyphicon' => 'font',
                'twb-layout' => 'inline'
            )
        ));
        $this->add($buttons);
    }
}