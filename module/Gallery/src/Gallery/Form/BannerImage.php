<?php
namespace Gallery\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;

class BannerImage extends Fieldset implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('banner_image');
        $this->setAttribute('id', 'imageBox');
        $this->setLabel('Image Name');
        $this->attributes['class'] = 'inline-collection collection-item';


        $this->add(array(
            'name' => 'image',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(//'class'=>'online-order-domain-input'
            ),
            'options' => array(// 'label' => 'Choose an image',
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
                'value' => 'Delete This Item',
                'title' => 'Delete This Item',
                'class' => 'button icon_button delete_button drop_collection_item',
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
                'validators' => array(
                    new Extension('jpg,jpeg,png,gif'),
                    new MimeType('image'),
                )
            )
        );
    }
}
