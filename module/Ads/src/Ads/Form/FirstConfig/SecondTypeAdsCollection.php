<?php
namespace Ads\Form\FirstConfig;

use Zend\Form\Fieldset;

class SecondTypeAdsCollection extends Fieldset
{
    private $count;

    public function __construct()
    {
        $this->count = 1;
        parent::__construct('second_type_ads');
        $this->setAttribute('id', 'second_type_ads_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'secondType',
            'options' => array(
               // 'label' => 'ADS_SECOND_TYPE',
               // 'description' => 'ADS_SECOND_TYPE_DESC',
                'count' => $this->count,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Ads\Form\FirstConfig\SecondTypeAds'
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
