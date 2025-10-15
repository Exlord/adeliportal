<?php

namespace NewsLetter\Form;

use Zend\Form\Fieldset;

class ConfigSelectValue extends Fieldset
{
    private $apiName = '';
    private $categories = array();

    public function __construct($apiName)
    {
        $this->apiName = $apiName;
        if (getSM()->has($this->apiName)) {
            $api = getSM($this->apiName);
            $this->categories = $api->getCategories();
        }
        parent::__construct('config-select-value');
        $this->setAttribute('id', 'select');
        //$this->setLabel('Select');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class' => 'col-md-4 col-sm-4'));


        $this->add(array(
            'name' => 'catId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Categories',
                'empty_option' => '-- Select --',
                'value_options' => $this->categories,
                'description' => ""
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'count',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Count'
            ),
            'attributes' => array(),
        ));


        $this->add(array(
            'name' => 'drop_collection_item',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'value' => t('Delete This Item'),
                'title' => t('Delete This Item'),
                'class' => 'btn btn-default drop_collection_item',
            ),
        ));
    }
}
