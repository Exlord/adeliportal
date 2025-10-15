<?php
namespace Category\Form;

use Zend\Form\Fieldset;

class CategoryImageCollection extends Fieldset
{
    private $count;

    public function __construct()
    {
        $this->count = 1;
        parent::__construct('image');
        $this->setAttribute('id', 'image_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'image',
            'options' => array(
                'label' => 'Image',
                'count' => $this->count,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Category\Form\CategoryImageValue'
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
                'title' => t('Add More Select Options'),
                'value' => t('Add More'),
                'class' => 'btn btn-default add_collection_item',
            ),
        ));
    }
}
