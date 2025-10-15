<?php
namespace Ads\Form\FirstConfig;

use Zend\Form\Fieldset;

class TimeAdsCollection extends Fieldset
{
    private $count;

    public function __construct()
    {
        $this->count = 1;
        parent::__construct('time_ads');
        $this->setAttribute('id', 'time_ads_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'timeAds',
            'options' => array(
               //'label' => 'ADS_TIME',
               // 'description' => 'ADS_TIME_DESC',
                'count' => $this->count,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Ads\Form\FirstConfig\TimeAds'
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
