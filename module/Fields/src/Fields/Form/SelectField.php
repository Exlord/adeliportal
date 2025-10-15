<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class SelectField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('select_field');
        $this->setAttribute('id', 'select_settings');

        $this->add(array(
            'name' => 'select_field_size',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Select Field Size',
                'description' => 'Default:1-Normal Drop-down List,if >1 u will have a flat list instead of drop-down and this value will be the number of visible items.',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 1,
                'data-max' => 10,
                'data-step' => 1,
                'value' => 1
            )
        ));

        $this->add(array(
            'name' => 'select_field_select_count',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Selectable Item Count',
                'description' => 'The number of the items that can be selected',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 1,
                'data-max' => 1000,
                'data-step' => 1,
                'value' => 1
            )
        ));

        $this->add(array(
            'type'=>'Zend\Form\Element\Radio',
            'name'=>'sort',
            'options'=>array(
                'label'=>'Sort',
                'value_options'=>array(
                    '0'=>'Not Sorted',
                    '1'=>'Ascending',
                    '2'=>'Descending',
                )
            )
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
            'name' => 'add_more_select_option',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'title' => t('Add More Select Options'),
                'value' => t('Add More'),
                'class' => 'button add_button add_collection_item',
            ),
        ));
    }
}
