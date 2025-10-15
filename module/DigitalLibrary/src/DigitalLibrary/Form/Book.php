<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace DigitalLibrary\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class Book extends BaseForm implements InputFilterProviderInterface
{
    private $isEdit;

    public function __construct($isEdit = false)
    {
        $this->isEdit = $isEdit;
        parent::__construct('book');
        $this->setAttribute('class', 'ajax_submit');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Title'
            ),
        ));

        $files = getSM('private_file_table')->getArray();
        $this->add(array(
            'name' => 'files',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Files',
                'description' => 'start typing to select the files created in the private files section',
                'value_options' => $files
            ),
            'attributes' => array(
                'id' => 'book-files',
                'class' => 'select2',
                'multiple' => 'multiple'
            )
        ));

        $catItems = getSM('category_item_table')->getItemsTreeByMachineName('books');
        $this->add(array(
            'name' => 'category',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Category',
                'description' => '',
                'value_options' => $catItems
            ),
            'attributes' => array(
                'class' => 'select2',
                'multiple' => 'multiple'
            )
        ));

        $fields = new Fieldset('fields');
        $this->add($fields);
        $this->inputFilters['fields'] = getSM()->get('fields_api')->loadFieldsByType('books', $this, $fields, $this->isEdit);

        $this->add(new Buttons('books'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $this->inputFilters['title'] = array(
            'name' => 'title',
            'filers' => array(
                new Filter\StringTrim(),
                new Filter\StripTags()
            )
        );

        $this->inputFilters['files'] = array(
            'name' => 'files',
            'required' => true
        );

        $this->inputFilters['category'] = array(
            'name' => 'category',
            'required' => false
        );

        $this->inputFilters['fields']['required'] = false;
        $this->inputFilters['fields']['allow_empty'] = true;

        return $this->inputFilters;
    }
}
