<?php
namespace Ads\Form\FourConfig;

use Zend\Form\Fieldset;

class FieldsAdsCollection extends Fieldset
{
    private $count;
    private $baseType;
    private $isRequest;

    public function __construct($baseType,$isRequest)
    {
        $this->count = 1;
        $this->baseType = $baseType;
        $this->isRequest = $isRequest;
        parent::__construct('fields_ads');
        $this->setAttribute('id', 'fields_ads_settings');

        $this->add(array(
            'type' => 'System\Form\Element\Collection',
            'name' => 'fieldsAds',
            'options' => array(
                'count' => $this->count,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Ads\Form\FourConfig\FieldsAds'
                ),
                'baseType'=>$this->baseType,
                'isRequest'=>$this->isRequest,
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
