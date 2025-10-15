<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Category\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Theme\API\Common;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\Validator\StringLength;
use System\Captcha\CaptchaFactory;
use Zend\Filter;
use Zend\InputFilter\FileInput;
use System\Filter\FilterHtml;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;


class Items extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('category-items');
        $this->setAttribute('class', 'ajax_submit');
    }

    public function addElements()
    {

        $this->add(array(
            'name' => 'parentId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Category Item Parent',
                'empty_option' => '-- Select --',
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'itemName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Category Item Name'
            ),
            'attributes' => array(
                'size' => 50
            ),
        ));

        $this->add(array(
            'name' => 'itemStatus',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Is Enabled ?'
            ),
        ));
        $this->add(array(
            'name' => 'itemOrder',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Order',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => -999,
                'data-max' => 999,
                'data-step' => 1,
            )
        ));

        /*$this->add(array(
            'name' => 'image',
            'type' => 'Zend\Form\Element\File',
            'options' => array(
                'label' => 'Image',
                'description' =>
                    'Allowed extensions are jpg, jpeg, png and gif<br/>' .
                    'Min size : 100x100 and Max size : 1500x1500<br/>' .
                    'Max file size : 1MB',
            ),
        ));*/

        $images = new \Category\Form\CategoryImageCollection();
        $this->add($images);


        $this->add(array(
            'name' => 'itemText',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Item Description'
            ),
            'attributes' => array(
                'cols' => 47
            )
        ));


        $this->add(array(
            'type' => 'System\Form\Buttons',
            'name' => 'buttons'
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
            'parentId' => array(
                'name' => 'parentId',
                'required' => false,
                'allow_empty' => true,
            ),
            'itemName' => array(
                'name' => 'itemName',
                'required' => true,
                'filters' => array(
                    new StripTags(),
                    new StringTrim()
                )
            ),
            'itemText' => array(
                'name' => 'itemText',
                'required' => false,
                'allow_empty' => true,
                'filters' => array(
                    new FilterHtml(),
                    new StringTrim(),
                )
            )
        );
    }
}
