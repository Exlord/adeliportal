<?php

namespace Gallery\Form;

use Zend\Form\Fieldset;

class ConfigGallery extends Fieldset
{
    public function __construct()
    {
        parent::__construct('config');
        $this->setAttribute('id', 'config');

        $this->add(array(
            'name' => 'transparent',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Transparent Background ?'
            ),
        ));

        //display : 1 => Random , 2 => Order
        $this->add(array(
            'name' => 'displayType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Display',
                'value_options' => array(
                    '1' => 'Random',
                    '2' => 'Order',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

    }
}
