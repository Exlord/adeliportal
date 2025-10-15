<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Page\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;

class PageImage extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('image');
        $this->setAttribute('id', 'pageConfig');

        $this->add(array(
            'name' => 'image',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(),
            'options' => array(
                'label' => 'Choose an image',
            ),
        ));

        $this->add(array(
            'name' => 'alt',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Alt Text',
                'description' => 'The photo does not show the text to be displayed'
            ),
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Title'
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
