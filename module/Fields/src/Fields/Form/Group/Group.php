<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/30/2014
 * Time: 2:41 PM
 */
namespace Fields\Form\Group;

use System\Form\BaseForm;
use Zend\InputFilter\InputFilterProviderInterface;

class Group extends BaseForm implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('field_group');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Text',
            'name' => 'title',
            'options' => array(
                'label' => 'Title',
            ),
        ));

        $this->add(array(
            'type' => 'Textarea',
            'name' => 'note',
            'options' => array(
                'label' => 'Note',
                'description' => 'prefix note on top of fields'
            ),
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'colSize',
            'options' => array(
                'label' => 'Column Class',
                'description' => 'exp: col-md-6 col-sm-12'
            ),
            'attributes' => array(
                'class' => 'text-left'
            )
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'class',
            'options' => array(
                'label' => 'Container Class',
                'description' => 'css class name for the container'
            ),
            'attributes' => array(
                'class' => 'text-left'
            )
        ));

        $this->add(array(
            'type' => 'Select',
            'name' => 'containerType',
            'options' => array(
                'label' => 'Container Type',
                'value_options' => array(
                    'panel' => 'Panel',
                    'fieldset' => 'Fieldset',
                    'div' => 'Normal Div+h3'
                ),
            ),
            'attributes' => array(
                'class' => 'text-left'
            )
        ));

        $this->add(array(
            'type' => 'Submit',
            'name' => 'submit',
            'options' => array(
                'label' => 'Save',
                'description' => 'css class name for the container',
                'glyphicon' => 'plus',
            ),
            'attributes' => array(
                'class' => 'text-left'
            )
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array();
    }
}