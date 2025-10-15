<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class KeyValuePairField extends Fieldset
{
    public function __construct()
    {
        parent::__construct('KeyValuePair_field');
        $this->setLabel('kay-value pair');
        $this->attributes['class'] = 'inline-collection collection-item';
        $this->add(array(
            'name' => 'field_key',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'value' => 'KEY'
            ),
        ));

        $this->add(array(
            'name' => 'field_value',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'value' => 'VALUE'
            ),
        ));
        $this->add(array(
            'name' => 'drop_collection_item',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'value' => 'Delete This Item',
                'title' => t('Delete This Item'),
                'class' => 'button icon_button delete_button drop_collection_item',
            ),
        ));

    }
}
