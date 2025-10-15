<?php
namespace SimpleOrder\Form;

use Zend\Form\Fieldset;

class CategoryCollection extends Fieldset
{
    public function __construct()
    {
        parent::__construct('category_collection');
        $this->setAttribute('id', 'select_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'orderCategory',
            'options' => array(
                'label' => 'simpleOrder_title_choice_product',
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'SimpleOrder\Form\Categories'
                )
            ),
            'attributes' => array(
                'class' => 'collection-container'
            ),
        ));

        $this->add(array(
            'name' => 'add_more_select_option',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'title' => t('application_link_add_more'),
                'value' => t('application_link_add_more'),
                'class' => 'button add_button add_collection_item',
            ),
        ));
    }
}
