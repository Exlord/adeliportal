<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace File\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use File\Model;

class FileField extends Fieldset //implements InputFilterProviderInterface
{
    private $fileTypes;
    private $maxSize;
    private $target;
    private $fileRequired;
    private $fileAllowEmpty;

    public function __construct($fileTypes, $maxSize, $target, $fileRequired = false, $fileAllowEmpty = true)
    {
        parent::__construct('file_field');
        $this->fileTypes = $fileTypes;
        $this->maxSize = $maxSize;
        $this->target = $target;
        $this->fileAllowEmpty = $fileAllowEmpty;
        $this->fileRequired = $fileRequired;

        $this->setLabel('File');
        $this->setAttribute('class', 'collection-item');
        $this->addElements();
    }


    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
/*       public function getInputFilterSpecification()
       {
           return array(
               'fPath' => array(
                   'type' => 'Zend\InputFilter\FileInput',
                   'required' => false,
                   'allow_empty' => true,
                   'filters' => array(
                       array('name' => 'Zend\Filter\File\RenameUpload',
                           'options' => array('target' => $this->target, 'randomize' => true,)),
                   ),
                   'validators' => array(
                       array('name' => 'Zend\Validator\File\UploadFile',),
                       array('name' => 'Zend\Validator\File\MimeType', 'options' => array('mimeType' => $this->fileTypes)),
                       array('name' => 'Zend\Validator\File\Size', 'options' => array('max' => $this->maxSize)),
                   ),
               ),
           );
       }*/

    protected function addElements()
    {
        $this->add(array(
            'name' => 'fName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'File Name'
            ),
            'attributes' => array(
                'size' => 100
            )
        ));

        $this->add(array(
            'name' => 'fTitle',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'File Title',
                'description' => 'Title attribute of the element. will be displayed if mouse hovers the element'
            ),
            'attributes' => array(
                'size' => 50
            )
        ));

        $this->add(array(
            'name' => 'fAlt',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Image Alt Text',
                'description' => 'Image Alt text wil be visible if the image is not loaded'
            ),
            'attributes' => array(
                'size' => 50
            )
        ));

        $this->add(array(
            'name' => 'fPath',
            'type' => 'Zend\Form\Element\File',
            'options' => array(
                'label' => 'File',
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

/*    protected function createInputFilter()
    {
        $filter = $this->getInputFilter();

        $textFilter = new Input();
        $textFilter->setRequired(true)
            ->getFilterChain()->attach(new \Zend\Filter\StringTrim());
        $filter->add($textFilter, 'fTitle');

        $fileFilter = new FileInput();
        $fileFilter
            ->setRequired($this->fileRequired)
            ->setAllowEmpty($this->fileAllowEmpty)
            ->getFilterChain()->attach(new \Zend\Filter\File\RenameUpload(
                array('target' => $this->target, 'randomize' => true,)));
        $fileFilter->getValidatorChain()
            ->attach(new \Zend\Validator\File\UploadFile())
            ->attach(new \Zend\Validator\File\MimeType(array('mimeType' => $this->fileTypes)))
            ->attach(new \Zend\Validator\File\Size(array('max' => $this->maxSize)));
        $filter->add($fileFilter, 'fPath');
        $this->setInputFilter($filter);
        return $filter;
    }*/

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
/*    public function getInputFilterSpecification()
    {
        return array(
            'fTitle' => array(
                'filters' => array(
                    new \Zend\Filter\StringTrim(),
                )
            ),
            'fPath' => array(
                'type' => 'Zend\InputFilter\FileInput',
                'required' => $this->fileRequired,
                'allow_empty' => $this->fileAllowEmpty,
                'validators' => array(
                    array('name' => 'Zend\Validator\File\UploadFile',),
//                    array('name' => 'Zend\Validator\File\MimeType', 'options' => array('mimeType' => $this->fileTypes)),
//                    array('name' => 'Zend\Validator\File\Size', 'options' => array('max' => $this->maxSize)),
                ),
                'filters' => array(
                    new \Zend\Filter\File\RenameUpload(array('target' => $this->target, 'randomize' => true,)),
                ),
            )
        );
    }*/
}
