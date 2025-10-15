<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Filter\StringTrim;
use Zend\Form\Fieldset;
use Zend\I18n\Validator\Alpha;
use Zend\InputFilter\InputFilterProviderInterface;

class CollectionField extends BaseConfig implements InputFilterProviderInterface
{
    public function __construct($entityType)
    {
        parent::__construct('collection_field');
        $this->setLabel('Collection Settings');
        $this->setOption('description', 'this field displays a collection of other fields');
        $this->setAttribute('id', 'collection_settings');

        $fields = getSM('fields_table')->getCollectionFieldsArray($entityType);
        $this->add(array(
            'name' => 'fields',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Fields',
                'description' => '',
                'value_options' => $fields,
            ),
            'attributes' => array(
                'class' => 'select2',
                'multiple' => 'multiple'
            )
        ));

        $this->add(array(
            'name' => 'itemCount',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Initial item count',
                'description' => 'how many fieldset should be created initially',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-max' => 20,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'name' => 'maxCount',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Max item count',
                'description' => 'how many fieldset totally can be created',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 1,
                'data-max' => 20,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'name' => 'allowAdd',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Allow add',
                'description' => 'Are new elements allowed to be added dynamically ?',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'allowRemove',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Allow remove',
                'description' => 'Are existing elements allowed to be removed dynamically ?',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'note',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Note',
                'description' => 'a description note for this collection',
            ),
            'attributes' => array(
                'style' => 'width:100%;',
                'rows' => 5,
            )
        ));

        $this->add(array(
            'name' => 'targetElementClass',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Target Element Class',
                'description' => 'exp: well well-sm',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'targetElementColumnClass',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Target Element Column Class',
                'description' => 'exp: col-md-12, col-md-6',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'targetElementLayout',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Target Element Layout',
                'value_options' => array(
                    'inline-collection' => 'Inline Collection',
                    'none' => 'Basic Form',
                    'inline' => 'Inline',
                    'horizontal' => 'Horizontal',
                ),
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $horizontalLayout = new Fieldset('horizontal_layout');
        $horizontalLayout->setLabel('Horizontal Layout Settings');
        $this->add($horizontalLayout);
        $horizontalLayout->add(array(
            'name' => 'label_class',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Label Class',
                'description' => 'exp: col-md-2, col-md-3',
            ),
            'attributes' => array(
                'class' => 'text-left'
            )
        ));
        $horizontalLayout->add(array(
            'name' => 'column_size',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Column Size',
                'description' => 'exp: md-10, md-9',
            ),
            'attributes' => array(
                'class' => 'text-left'
            )
        ));

        $view = new Fieldset('view');
        $view->setLabel('Rendered View Settings');
        $this->add($view);

        $view->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'description' => 'how data should be rendered in the output',
                'value_options' => array(
                    'none' => 'None (array:should be manually rendered in view)',
                    'comma' => 'Comma Separated(only if there is just 1 field)',
                    'space' => 'Space Separated(only if there is just 1 field)',
                    'simple-table' => 'Table without headers',
                    'table' => 'Table with headers',
                    'block-dl' => 'Block - DL',
                    'block-dl-h' => 'Block - DL Horizontal',
                ),
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $view->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Render Title',
                'description' => '',
            ),
            'attributes' => array()
        ));

        $view->add(array(
            'name' => 'note',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Render Description Note',
                'description' => '',
            ),
            'attributes' => array()
        ));

        $view->add(array(
            'name' => 'class',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Class',
                'description' => 'exp: well well-sm',
            ),
            'attributes' => array()
        ));

        $view->add(array(
            'name' => 'columnClass',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Column Class',
                'description' => 'exp: col-md-12, col-md-6',
            ),
            'attributes' => array()
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
        return array(
            'fields' => array(
                'name' => 'fields',
                'required' => false,
                'allow_empty' => true,
                'filters' => array(),
                'validators' => array(),
            )
        );
    }
}
