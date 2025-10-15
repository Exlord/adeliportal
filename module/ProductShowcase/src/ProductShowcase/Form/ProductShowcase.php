<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace ProductShowcase\Form;

use System\Form\BaseForm;
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

class ProductShowcase extends BaseForm implements InputFilterProviderInterface
{
    private $catIdArray = array();

    public function __construct($catIdArray)
    {
        $this->catIdArray = $catIdArray;
        parent::__construct('product_showcase_form');
        $this->setAttribute('class', 'normal-form ajax_submit');
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

        $this->add(array(
            'name' => 'catId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Categories',
                'value_options' => $this->catIdArray,
                'empty_option' => '-- Select --',
            ),
            'attributes' => array(
                'class' => 'select2',
                'multiple' => 'multiple',
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

        $this->add(array(
            'name' => 'order',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Order'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => -999,
                'data-max' => 999,
                'data-step' => 1,
            )
        ));


        $fields = new Fieldset('fields');
        $this->add($fields);
        $this->inputFilters['fields'] = getSM()->get('fields_api')->loadFieldsByType('product_showcase', $this, $fields);

        $this->add(new \System\Form\Buttons('product_showcase_form'));

        $images = new \ProductShowcase\Form\ImageCollection();
        $this->add($images);

//        $this->addInputFilters(array('fields' => $inputFilters));

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
            'filters' => array(
                new Filter\StringTrim(),
                new Filter\StripTags()
            )
        );
        $this->inputFilters['catId'] = array(
            'name' => 'catId',
            'required' => false
        );

        return $this->inputFilters;
    }
}
