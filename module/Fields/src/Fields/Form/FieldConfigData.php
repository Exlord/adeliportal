<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class FieldConfigData extends Fieldset
{
    public function __construct($entityType)
    {
        parent::__construct('fieldConfigData');
        $this->setAttribute('id', 'fieldConfigData');

        $this->add(array(
            'type' => 'Fields\Form\TextField',
            'name' => 'text_field',
            'options' => array(
                'label' => 'Text Field Settings'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\LongTextField',
            'name' => 'long_text_field',
            'options' => array(
                'label' => 'Long Text Field Settings'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\CheckBoxField',
            'name' => 'checkBox_field',
            'options' => array(
                'label' => 'CheckBox Field Settings'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\RadioField',
            'name' => 'radio_field',
            'options' => array(
                'label' => 'Radio Field Settings'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\SelectField',
            'name' => 'select_field',
            'options' => array(
                'label' => 'Select Field Settings'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\UniqueCodeField',
            'name' => 'uniqueCode_field',
            'options' => array(
                'label' => 'UniqueCode Field Settings',
                'description' => 'This field generates a readonly unique code'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\BarcodeField',
            'name' => 'barcode_field',
            'options' => array(
                'label' => 'Barcode Field Settings',
                'description' => 'This field generates a readonly unique code and displays it as a barcode.'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\CurrentDateField',
            'name' => 'currentDate_field',
            'options' => array(
                'label' => 'Current Date Field Settings',
                'description' => 'This field saves the forms submit date.'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\FileUploadField',
            'name' => 'fileUpload_field',
            'options' => array(
                'label' => 'File Upload Field Settings',
                'description' => 'This field uploads and saves a file to server'
            )
        ));

        $this->add(array(
            'type' => 'Fields\Form\ConstantField',
            'name' => 'constant_field',
            'options' => array(
                'label' => 'Static Text Settings',
                'description' => 'this field displays a static text'
            )
        ));

//        $this->add(array(
//            'type' => 'Fields\Form\CollectionField',
//            'name' => 'collection_field',
//            'options' => array(
//                'label' => 'Collection Settings',
//                'description' => 'this field displays a collection of other fields'
//            )
//        ));
        $this->add(new CollectionField($entityType));

    }
}
