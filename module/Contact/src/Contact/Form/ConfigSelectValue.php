<?php

namespace Contact\Form;

use Zend\Form\Fieldset;

class ConfigSelectValue extends Fieldset
{

    public function __construct()
    {
        parent::__construct('config-select-value');
        $this->setAttribute('id', 'select');
      //  $this->setLabel('Select');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class'=>'col-md-3 col-sm-4'));


        $this->add(array(
            'name' => 'selectName',
            'type' => 'Zend\Form\Element\Text',
            'options'=>array(
            ),
            'attributes' => array(
                'class'=>'online-order-domain-input'
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
                'value' => t('Delete This Item'),
                'title' => t('Delete This Item'),
                'class' => 'btn btn-default drop_collection_item',
            ),
        ));
    }
}
