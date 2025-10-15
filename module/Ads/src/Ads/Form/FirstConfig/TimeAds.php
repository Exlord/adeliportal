<?php

namespace Ads\Form\FirstConfig;

use Zend\Form\Fieldset;

class TimeAds extends Fieldset
{

    public function __construct()
    {
        parent::__construct('time_ads_value');
        $this->setAttribute('id', 'time_ads');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class'=>'col-md-3 col-sm-4'));

        $this->add(array(
            'name' => 'timeAdsValue',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    '1' => 'ADS_1MONTH',
                    '2' => 'ADS_2MONTH',
                    '3' => 'ADS_3MONTH',
                    '4' => 'ADS_4MONTH',
                    '5' => 'ADS_5MONTH',
                    '6' => 'ADS_6MONTH',
                    '7' => 'ADS_7MONTH',
                    '8' => 'ADS_8MONTH',
                    '9' => 'ADS_9MONTH',
                    '10' => 'ADS_10MONTH',
                    '11' => 'ADS_11MONTH',
                    '12' => 'ADS_12MONTH',
                )
            ),
            'attributes' => array(
                'class' => '',
            )
        ));

        $this->add(array(
            'name' => 'drop_collection_item',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'value' => t('Delete This Item'),
                'title' => t('Delete This Item'),
                'class' => 'btn btn-default drop_collection_item',
            ),
        ));
    }
}
