<?php
namespace Ads\Form\NewTypeConfig;

use Zend\Form\Fieldset;

class BaseTypeAdsCollection extends Fieldset
{
    private $count;

    public function __construct()
    {
        $this->count = 1;
        parent::__construct('base_type_ads');
        $this->setAttribute('id', 'base_type_ads_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'baseType',
            'options' => array(
               // 'label' => 'ADS_BASE_TYPE',
              //  'description' => 'ADS_BASE_TYPE_DESC',
                'count' => $this->count,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Ads\Form\NewTypeConfig\BaseTypeAds'
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
