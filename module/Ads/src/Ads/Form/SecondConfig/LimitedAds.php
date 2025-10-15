<?php

namespace Ads\Form\SecondConfig;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;

class LimitedAds extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('limited_ads_value');
        $this->setAttribute('id', 'limited_ads');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class' => 'col-md-3 col-sm-4'));
    }

    public function initialize($parent)
    {
        $baseType = $parent->getOption('baseType');
        $isRequest = $parent->getOption('isRequest');
        $timeAdsArray = array();
        $secondTypeAdsArray = array();
        $config_first = getConfig('ads_first_config_' . $baseType.'_'.$isRequest)->varValue;
        if (isset($config_first['time_ads']['timeAds'])) {
            foreach ($config_first['time_ads']['timeAds'] as $row)
                $timeAdsArray[$row['timeAdsValue']] = $row['timeAdsValue'];
        }
        if (isset($config_first['second_type_ads']['secondType'])) {
            foreach ($config_first['second_type_ads']['secondType'] as $row)
                $secondTypeAdsArray[$row['idSecondTypeAdsValue']] = $row['secondTypeAdsValue'];
        }
        if($isRequest)
            $regTypeArray[] = 'ADS_REQUEST';
        else
            $regTypeArray[] = 'ADS_TRANSFER';

        $this->add(array(
            'name' => 'regType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
              //  'label' => 'ADS_REG_TYPE',
                'value_options' => $regTypeArray
            ),
            'attributes' => array(
                'class' => 'display_none',
            )
        ));

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
            'name' => 'timeAds',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_TIME',
                'value_options' => $timeAdsArray
            ),
            'attributes' => array(
                'class' => '',
            )
        ));

        $this->add(array(
            'name' => 'countInDay',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'ADS_COUNT_IN_DAY',
                'description' => 'ADS_COUNT_IN_DAY_DESC',
            )
        ));

        $this->add(array(
            'name' => 'countCatItem',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'ADS_COUNT_CAT_ITEM',
                'description' => 'ADS_COUNT_CAT_ITEM_DESC',
            )
        ));

        $this->add(array(
            'name' => 'haveLink',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    '0' => 'ADS_NOT_HAVE_LINK',
                    '1' => 'ADS_HAVE_LINK',
                )
            ),
            'attributes' => array(
                'class' => '',
            )
        ));


        $this->add(array(
            'name' => 'countImage',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'ADS_COUNT_IMAGE',
                //'description'=>'ADS_COUNT_CAT_ITEM_DESC',
            )
        ));

        $this->add(array(
            'name' => 'starPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => t('ADS_STAR_PRICE') . ' ' . t(getCurrency()),
                'description' => 'ADS_STAR_PRICE_DESC',
            ),
            'attributes' => array(
                'class' => 'num withcomma',
            )
        ));

        $this->add(array(
            'name' => 'image',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(),
            'options' => array(
                'label' => 'Choose an image',
            ),
        ));

        $this->add(array(
            'name' => 'showTitlePack',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_SHOW_TITLE',
                'description' => 'ADS_SHOW_TITLE_DESC',
                'value_options' => array(
                    '1' => 'ADS_YES',
                    '0' => 'ADS_NO',
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

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'image' => array(
                'name' => 'image',
                'required' => false,
                'filters' => array(),
                'validators' => array(
                    new ImageSize(array('maxWidth' => 2000, 'maxHeight' => 2000)),
                    new MimeType('image'),
                    new Extension('jpg,jpeg,png,gif'),
                    new FilesSize(array('max' => 20480)),
                )
            ));
    }
}
