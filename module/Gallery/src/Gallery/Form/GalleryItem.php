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


class GalleryItem extends BaseForm implements InputFilterProviderInterface
{
    private $groupSelect = array();
    private $type = '';

    public function __construct($type, $groupSelect)
    {
        $this->groupSelect = $groupSelect;
        $this->type = $type;
        parent::__construct('item');
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
            'name' => 'hits',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'groupId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Groups',
                'value_options' => $this->groupSelect
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'order',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Order',
                'description' => 'To prioritize the display of each group is'
            ),
        ));

        $this->add(array(
            'name' => 'url',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Url',
                'description' => 'Foe Example : http://azaript.com'
            ),
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


        if ($this->type != 'banner') {
            $descWidth = '';
            $descHeight = '';
            switch ($this->type) {
                case 'slider' :
                case 'banner' :
                case 'imageBox' :
                    $descWidth = 'Width of the image to display ( If you do not enter a value, the default value Group is selected )';
                    $descHeight = 'Height of image for display ( If you do not enter a value, the default value Group is selected )';
                    break;
                case 'gallery' :
                    $descWidth = 'Width of image for display in a tiny ( If you do not enter a value, the default value Group is selected )';
                    $descHeight = 'Height of image for display in a tiny ( If you do not enter a value, the default value Group is selected )';
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

        $this->add(new \System\Form\Buttons('item_form'));
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
            ),
        );
    }
}
