<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/14/14
 * Time: 10:42 AM
 */

namespace ECommerce\Form\Product\Types;


use ECommerce\API\Product;
use Zend\Form\Fieldset;

class PhysicalItem extends Fieldset
{
    public function __construct()
    {
        parent::__construct(Product::TYPE_PHYSICAL);
        $this->setLabel('Physical Item');
        $this->setAttribute('class', 'no-border no-bg hidden');
        $this->setAttribute('id', 'product_type_' . Product::TYPE_PHYSICAL);

        $this->add(array(
            'name' => 'weight',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Weight'
            ),
            'attributes' => array(
                'size' => 25
            )
        ));

        //TODO weight class

        $this->add(array(
            'name' => 'width',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Width'
            ),
            'attributes' => array(
                'size' => 25
            )
        ));

        $this->add(array(
            'name' => 'length',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Length'
            ),
            'attributes' => array(
                'size' => 25
            )
        ));

        $this->add(array(
            'name' => 'height',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Height'
            ),
            'attributes' => array(
                'size' => 25
            )
        ));

        //TODO width/height/length class
    }
} 