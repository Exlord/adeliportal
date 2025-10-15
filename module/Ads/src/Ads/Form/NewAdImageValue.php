<?php

namespace Ads\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;

class NewAdImageValue extends Fieldset implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('image-value');
        $this->setAttribute('id', 'image');
        // $this->setLabel('Select');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class' => 'col-md-4 col-sm-4'));


        $this->add(array(
            'name' => 'imageValue',
            'type' => 'Zend\Form\Element\File',
            'options' => array(
                'label' => 'Image',
                /* 'description' =>
                     'Allowed extensions are jpg, jpeg, png and gif<br/>' .
                     'Min size : 100x100 and Max size : 1500x1500<br/>' .
                     'Max file size : 1MB',*/
            ),
        ));

        $this->add(array(
            'name' => 'imageTitle',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Title'
            ),
            'attributes' => array(
                'cols' => 47
            )
        ));

        $this->add(array(
            'name' => 'imageAlt',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Alt'
            ),
            'attributes' => array(
                'cols' => 47
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
            'imageValue' => array(
                'name' => 'imageValue',
                'require'=>false,
                'allow_empty'=>true,
                'validators' => array(
                    new Extension('jpg,jpeg,png,gif'),
                    new MimeType('image')
                )
            )
        );
    }
}
