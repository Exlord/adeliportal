<?php

namespace Ads\Form\NewTypeConfig;

use Zend\Form\Fieldset;

class BaseTypeAds extends Fieldset
{

    public function __construct()
    {
        parent::__construct('base_type_ads_value');
        $this->setAttribute('id', 'base_type_ads');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class'=>'col-md-3 col-sm-4'));

        $this->add(array(
            'name'=>'baseTypeAdsValue',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>'Name'
            ),
        ));

        $this->add(array(
            'name'=>'baseTypeAdsMachineName',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>'ADS_MACHINE_NAME'
            ),
        ));

        $this->add(array(
            'name'=>'idBaseTypeAdsValue',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>'Id',
                'description'=>'ADS_ID_NOT_DUPLICATE',
            ),
        ));

        $this->add(array(
            'name' => 'isRequest',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_IS_REQUEST',
                'value_options' => array(
                    '0' => 'ADS_IS_NOT_REQUEST',
                    '1' => 'ADS_IS_REQUEST',
                )
            ),
            'attributes' => array(
                'class' => '',
            )
        ));

        $this->add(array(
            'name'=>'priceOneEmail',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>t('ADS_PRICE_ONE_EMAIL'). ' ' . t(getCurrency()),
               // 'description'=>'ADS_VIEW_PRICE_DESC',
            ),
        ));

        $this->add(array(
            'name'=>'priceOneSms',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>t('ADS_PRICE_ONE_SMS'). ' ' . t(getCurrency()),
               // 'description'=>'ADS_VIEW_PRICE_DESC',
            ),
        ));

        $this->add(array(
            'name' => 'isGoogleMap',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_IS_GOOGLE_MAP',
                'value_options' => array(
                    '0' => 'ADS_IS_NOT',
                    '1' => 'ADS_IS',
                )
            ),
            'attributes' => array(
                'class' => '',
            )
        ));

        $this->add(array(
            'name' => 'rateStatus',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_IS_RATING',
                'value_options' => array(
                    '0' => 'ADS_NO',
                    '1' => 'ADS_YES',
                )
            ),
            'attributes' => array(
                'class' => '',
            )
        ));

        $this->add(array(
            'name'=>'adViewPrice',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>t('ADS_VIEW_PRICE'). ' ' . t(getCurrency()),
                'description'=>'ADS_VIEW_PRICE_DESC',
            ),
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
