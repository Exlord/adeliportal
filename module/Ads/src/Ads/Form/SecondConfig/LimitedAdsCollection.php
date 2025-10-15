<?php
namespace Ads\Form\SecondConfig;

use Zend\Form\Fieldset;

class LimitedAdsCollection extends Fieldset
{
    private $count;
    private $baseType;
    private $isRequest;

    public function __construct($baseType,$isRequest)
    {
        $this->count = 1;
        $this->baseType = $baseType;
        $this->isRequest = $isRequest;
        parent::__construct('limited_ads');
        $this->setAttribute('id', 'limited_ads_settings');

        $this->add(array(
            'type' => 'System\Form\Element\Collection',
            'name' => 'limitedAds',
            'options' => array(
                'count' => $this->count,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Ads\Form\SecondConfig\LimitedAds'
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
