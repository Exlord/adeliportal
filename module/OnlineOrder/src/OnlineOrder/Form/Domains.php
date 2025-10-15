<?php
namespace OnlineOrder\Form;

use Zend\Form\Fieldset;

class Domains extends Fieldset
{
    private $countDomain;

    public function __construct($countDomain)
    {
        $this->countDomain = $countDomain;
        parent::__construct('domains');
        $this->setAttribute('id', 'select_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'domain',
            'options' => array(
                'label' => 'Domains',
                'count' => $this->countDomain,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'OnlineOrder\Form\DomainCustomer'
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
