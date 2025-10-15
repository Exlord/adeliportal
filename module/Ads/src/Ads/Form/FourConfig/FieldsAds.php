<?php

namespace Ads\Form\FourConfig;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;

class FieldsAds extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('fields_ads_value');
        $this->setAttribute('id', 'fields_ads');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class'=>'col-md-3 col-sm-4'));
    }

    public function initialize($parent)
    {
        $baseType = $parent->getOption('baseType');
        $isRequest = $parent->getOption('isRequest');

        $fields_0_array = array(
            'title'=>'Title',
            'text'=>'Description',
            'address'=>'Address',
            'name'=>'Name',
            'mobile'=>'Mobile',
            'stateId'=>'State',
            'cityId'=>'City',
            'baseType'=>'ADS_BASE_TYPE',
            'secondType'=>'ADS_SECOND_TYPE',
        );

        $fields_1_array = array(
            'tbl_ads.title'=>'Title',
            'tbl_ads.text'=>'Description',
            'tbl_ads.address'=>'Address',
            'tbl_ads.name'=>'Name',
            'tbl_ads.mobile'=>'Mobile',
            'tbl_ads.stateId'=>'State',
            'tbl_ads.cityId'=>'City',
            'tbl_ads.baseType'=>'ADS_BASE_TYPE',
            'tbl_ads.secondType'=>'ADS_SECOND_TYPE',
        );
        $fields_0 = getSM('fields_table')->getByEntityType('ads_' . $baseType . '_' . 0);
        if ($fields_0) {
            foreach ($fields_0 as $f) {
                $name = $f->fieldMachineName;
                $fields_0_array[$name]= $f->fieldName;
            }
        }

        $fields_1 = getSM('fields_table')->getByEntityType('ads_' . $baseType . '_' . 1);
        if ($fields_1) {
            foreach ($fields_1 as $f) {
                $name = 'f.' . $f->fieldMachineName;
                $fields_1_array[$name]= $f->fieldName;
            }
        }

        $typeArray=array(
            'equalTo'=>'equalTo',
            'notEqualTo'=>'notEqualTo',
            'in'=>'in',
            'notIn'=>'notIn',
            'greaterThan'=>'greaterThan',
            'lessThan'=>'lessThan',
            'like'=>'Like',
            'notLike'=>'notLike',
            'between'=>'between',
            'or_equalTo'=>'or_equalTo',
            'or_like'=>'or_like',
        );

        $this->add(array(
            'name' => 'base0',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
              //  'label' => 'ADS_REG_TYPE',
                'value_options' => $fields_0_array
            ),
            'attributes' => array(
               // 'class' => 'display_none',
            )
        ));

        $this->add(array(
            'name' => 'base1',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $fields_1_array,
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'multiple' => 'multiple',
                'size' => 10,
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'value_options' => $typeArray
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
        // TODO: Implement getInputFilterSpecification() method.
        return array();
    }
}
