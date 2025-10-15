<?php
namespace Contact\Form;

use Zend\Form\Fieldset;

class ConfigSelect extends Fieldset
{
    private $count;

    public function __construct($count)
    {
        $this->count = $count;
        parent::__construct('select');
        $this->setAttribute('id', 'select_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'select',
            'options' => array(
                'label' => 'Type',
                'description'=>'Contact_TYPE_DESC',
                'count' => $this->count,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Contact\Form\ConfigSelectValue'
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
