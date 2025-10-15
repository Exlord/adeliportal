<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/12/14
 * Time: 1:57 PM
 */

namespace ECommerce\Form\Product;


use Zend\Form\Fieldset;

class General extends Fieldset
{
    public function __construct()
    {
        parent::__construct('general');
        $this->setLabel('General Details');
        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Product Name'
            ),
            'attributes' => array(
                'size' => 50
            )
        ));

        $this->add(array(
            'name' => 'note',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Description'
            ),
            'attributes' => array(
                'cols' => 50,
                'rows' => 5
            )
        ));
    }
} 