<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class CheckBoxField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('checkBox_field');
        $this->setAttribute('id', 'checkBox_settings');
        $this->add(array(
            'name' => 'checkBox_field_showAsButton',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Show As Selectable Button',
                'description' => 'Whether to show this field as Button with the label inside witch can be selected/deselected',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'keyValuePairs',
            'options' => array(
                'label' => 'Kay-Value Pairs',
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Fields\Form\KeyValuePairField'
                )
            ),
            'attributes' => array(
                'class' => 'collection-container'
            ),
        ));
//        $this->add(new MultiLangKeyValuePair(1));

        $this->add(array(
            'name' => 'add_more_checkbox',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'title' => 'Add More Checkboxes',
                'value' => 'Add More',
                'class' => 'button add_button add_collection_item',
            ),
        ));
    }
}
