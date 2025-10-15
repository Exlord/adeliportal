<?php

namespace Ads\Form\FirstConfig;

use Zend\Form\Fieldset;

class SecondTypeAds extends Fieldset
{

    public function __construct()
    {
        parent::__construct('second_type_ads_value');
        $this->setAttribute('id', 'second_type_ads');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class'=>'col-md-3 col-sm-4'));

        $this->add(array(
            'name'=>'secondTypeAdsValue',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>'Name'
            ),
        ));
        $this->add(array(
            'name'=>'secondTypeAdsMachineName',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>'ADS_MACHINE_NAME'
            ),
        ));
        $this->add(array(
            'name'=>'idSecondTypeAdsValue',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>'Id',
                'description'=>'ADS_ID_NOT_DUPLICATE',
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
