<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/12/14
 * Time: 2:20 PM
 */

namespace ECommerce\Form\Product;


use ECommerce\API\Product;
use Zend\Form\Fieldset;

class Details extends Fieldset
{
    public function __construct($categories)
    {
        $categories = array('' => 'Uncategorised') + $categories;
        parent::__construct('details');
        $this->setLabel('Details');

        $this->add(array(
            'name' => 'price',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Price'
            ),
            'attributes' => array(
                'size' => 50
            )
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Is Enabled ?',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'category',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'label' => 'Categories',
                'value_options' => $categories,
                'description' => "To categorise products create a category with machine name of 'commerce_product'"
            ),
            'attributes' => array()
        ));
    }
} 