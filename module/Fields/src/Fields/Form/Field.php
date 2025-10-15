<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Fields\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Filter\StringToLower;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Filter\Word\DashToUnderscore;
use Zend\Filter\Word\SeparatorToSeparator;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\I18n\Filter\Alnum;
use Zend\I18n\Filter\Alpha;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Field extends BaseForm implements InputFilterProviderInterface
{
    private $entityTypes;
    private $entityType;

    public function __construct($entityTypes, $selectedEntityType = null)
    {
        $this->entityTypes = $entityTypes;
        $this->entityType = $selectedEntityType;
        parent::__construct('field_form');
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'fieldType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Field Type',
                'value_options' => getSM('fields_table')->fieldTypes
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'fieldName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Field Name'
            ),
        ));

        $this->add(array(
            'name' => 'fieldMachineName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Field Machine Name',
                'description' => 'Machine name can only contain English Alphabets and Numbers and _ and no space and no number at the beginning'
            ),
            'attributes' => array(
                'class' => 'left-align'
            )
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Is Enabled ?'
            ),
        ));

        $this->add(array(
            'name' => 'collection',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Does this field belongs to a collection ?'
            ),
        ));

        $this->add(array(
            'name' => 'fieldOrder',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Order'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-max' => 1000,
                'data-step' => 1,
                'value' => 0
            )
        ));

        $this->add(array(
            'name' => 'entityType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Entity Type',
                'empty_option' => '-- Select --',
                'value_options' => $this->entityTypes
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'fieldDefaultValue',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Default Value'
            ),
            'attributes' => array(
                'size' => 75
            )
        ));


        $this->add(array(
            'name' => 'fieldPrefix',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Field Prefix',
                'description' => "The text to show before the field's input.will be used in place of field's label"
            ),
            'attributes' => array(
                'size' => 50,
            )
        ));

        $this->add(array(
            'name' => 'fieldPostfix',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Field Postfix',
                'description' => 'The text to show after the fields input'
            ),
            'attributes' => array(
                'size' => 50,
            )
        ));

        $this->add(array(
            'name' => 'fieldDisplayTemplate',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Field Display Template',
                'description' => 'How to show the fields value to visitors? <br/>The default is LABEL FIELD<br/> PlaceHolders :<br/> LABEL : this tag will be replaced with the name of the field<br/>FIELD : this tag will be replaced with the content of the field'
            ),
            'attributes' => array()
        ));


//        $this->add(array(
//            'type' => 'Fields\Form\FieldConfigData',
//            'name' => 'fieldConfigData',
//            'options' => array(
//                'label' => ''
//            )
//        ));
        $this->add(new FieldConfigData($this->entityType));

        $this->add(new Filters());
        $this->add(new Validators());
        $this->add(new Buttons('field'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $inputs = array();
        $inputs['fieldName'] = array(
            'required' => true,
            'allow_empty' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            )
        );

        $inputs['fieldMachineName'] = array(
            'required' => true,
            'allow_empty' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                array('name' => 'Word\DashToUnderscore'),
                array('name' => 'System\Filter\Word\SpaceToUnderscore'),
            ),
            'validators' => array(
                array('name' => 'System\Validator\MachineName'),
            )
        );

        return $inputs;
    }
}
