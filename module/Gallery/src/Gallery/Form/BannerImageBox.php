<?php
namespace Gallery\Form;

use Zend\Form\Fieldset;

class BannerImageBox extends Fieldset
{

    public function __construct()
    {
        parent::__construct('banner_image_box');
        $this->setAttribute('id', 'select_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'imageBox',
            'options' => array(
                'label' => 'Upload Images',
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Gallery\Form\BannerImage'
                ),

            ),
            'attributes' => array(
                'class' => 'collection-container'
            ),
        ));

        $this->add(array(
            'name' => 'add_more_select_option',
            'options' => array(
                'label' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'title' => 'Add More Select Options',
                'value' => t('Add More'),
                'class' => 'button add_button add_collection_item',
            ),
        ));
    }
}
