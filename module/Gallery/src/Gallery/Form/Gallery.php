<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Gallery\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;


class Gallery extends BaseForm
{
    private $_position = array();
    private $type = '';

    public function __construct($type, $position = array())
    {
        $this->_position = $position;
        $this->type = $type;
        parent::__construct('Groups');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
            'method' => 'post'
        ));

    }

    public function addElements()
    {
        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'groupName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name'
            ),
        ));

        if ($this->type == 'banner') {

            $this->add(array(
                'name' => 'position',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Position',
                    'value_options' => $this->_position,
                    'description' => 'Select in witch area of the template this block should be shown'
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));

            $this->add(array(
                'name' => 'publishUp',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Publish Up'
                ),
            ));

            $this->add(array(
                'name' => 'publishDown',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Publish Down'
                ),
                'required' => true,
                'validators' => array(
                    array('name' => 'not_empty',),
                ),
            ));

            $this->add(array(
                'name' => 'reloadType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Reload Type',
                    'value_options' => array(
                        '0' => 'Page Load',
                        '30' => '30 Second',
                        '60' => '60 Second',
                        '300' => '5 Minuets',
                        '600' => '10 Minuets',
                        '900' => '15 Minuets',
                    ),
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
        }

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status',
                'value_options' => array(
                    '0' => 'Not Approved',
                    '1' => 'Approved'
                ),
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'groupText',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Description'
            ),
            'required' => false,
            'validators' => array(
                array('name' => 'string_length', 'options' => array('min' => 5, 'max' => 2000),),
            ),
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));

        if ($this->type != 'banner') {
            $descWidth = '';
            $descHeight = '';
            switch ($this->type) {
                case 'slider' :
                    //case 'banner' :
                case 'imageBox' :
                    $descWidth = 'Width of the images to display';
                    $descHeight = 'Height of images for display';
                    break;
                case 'gallery' :
                    $descWidth = 'Width of images for display in a tiny';
                    $descHeight = 'Height of images for display in a tiny';
                    break;

            }
            $this->add(array(
                'name' => 'width',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Width',
                    'description' => $descWidth
                ),
            ));

            $this->add(array(
                'name' => 'height',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Height',
                    'description' => $descHeight
                ),
            ));
        }


        if ($this->type == 'gallery') {
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

            $this->add(array(
                'name' => 'showType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'GALLERY_SHOW_GALLERY_PAGE',
                    'value_options' => array(
                        '0' => 'GALLERY_NOT_SHOW',
                        '1' => 'GALLERY_SHOW'
                    ),
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
        }


        $config = new \Gallery\Form\ConfigGallery();
        $this->add($config);


        $this->add(new \System\Form\Buttons('groups_form'));
    }

    public function addInputFilters()
    {
        if ($this->type != 'banner') {
            $filter = $this->getInputFilter();
            $width = $filter->get('width');
            $width->setAllowEmpty(false);
            $width->setRequired(true);
            $height = $filter->get('height');
            $height->setAllowEmpty(false);
            $height->setRequired(true);
        }
        if ($this->type == 'gallery') {
            $image = $this->getInputFilter()->get('image');
            $image->setRequired(false);
            $image->getValidatorChain()
                ->attach(new Extension('jpg,jpeg,gif,png'))
                ->attach(new MimeType('image'));
        }
    }

//    /**
//     * Should return an array specification compatible with
//     * {@link Zend\InputFilter\Factory::createInputFilter()}.
//     *
//     * @return array
//     */
//    public function getInputFilterSpecification()
//    {
//        return array(
//            'image' => array(
//                'name' => 'image',
//                'required' => true,
//                'validators' => array(
//                    new Extension('jpg,jpeg,gif,png'),
//                    new MimeType('image')
//                )
//            ),
//        );
//    }
}
