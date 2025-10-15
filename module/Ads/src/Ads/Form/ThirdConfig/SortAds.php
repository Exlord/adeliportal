<?php

namespace Ads\Form\ThirdConfig;

use Zend\Form\Fieldset;

class SortAds extends Fieldset
{

    public function __construct()
    {
        parent::__construct('sort_ads_value');
        $this->setAttribute('id', 'sort_ads');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class'=>'col-md-3 col-sm-4'));
    }

    public function initialize($parent)
    {
        $baseType = $parent->getOption('baseType');
        $isRequest = $parent->getOption('isRequest');
        $secondTypeAdsArray = array();
        $config_first = getConfig('ads_first_config_'.$baseType.'_'.$isRequest)->varValue;
        if (isset($config_first['second_type_ads']['secondType'])) {
            foreach ($config_first['second_type_ads']['secondType'] as $row)
                $secondTypeAdsArray[$row['idSecondTypeAdsValue']] = $row['secondTypeAdsValue'];
        }

        $this->add(array(
            'name' => 'secondType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $secondTypeAdsArray
            ),
            'attributes' => array(
                'class' => '',
            )
        ));

        $this->add(array(
            'name' => 'isHomePage',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_IS_HOME_PAGE',
                'description' => 'ADS_IS_HOME_PAGE_DESC',
                'value_options' => array(
                    '0'=>'ADS_IS_NOT',
                    '1'=>'ADS_IS',
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
