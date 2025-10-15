<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class RadioField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('radio_field');
        $this->setAttribute('id', 'radio_settings');
        $this->add(array(
            'name' => 'radio_field_showAsButton',
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
                'count' => 2,
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
//        $this->add(new MultiLangKeyValuePair(2));

        $this->add(array(
            'name' => 'add_more_radio',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'title' => 'Add More Radio Buttons',
                'value' => 'Add More',
                'class' => 'button add_button add_collection_item',
            ),
        ));
    }
}
