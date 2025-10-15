<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace EducationalCenter\Form;

use System\Filter\FilterHtml;
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

class Workshop extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('workshop');
        $this->setAttribute('class', 'ajax_submit');
        $this->setAttribute('data-cancel', url('admin/educational-center/workshop'));
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

        $catItems = getSM('category_item_table')->getItemsTreeByMachineName('ec_workshops');

        $this->add(array(
            'name' => 'catId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Category',
                'empty_option' => '-- Select --',
                'value_options' => $catItems,
                'help-block' => 'to categorize workshops create a category with "ec_workshops" machine name.'
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'code',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Workshop Code'
            ),
        ));

        $this->add(array(
            'name' => 'note',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Note'
            ),
            'attributes' => array(
                'id' => 'note'
            )
        ));

        $this->add(new Buttons('workshop', array(Buttons::SPAM, Buttons::CSRF, Buttons::SAVE, Buttons::SAVE_NEW, Buttons::SAVE_CLOSE, Buttons::CANCEL)));
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
            'title' => array(
                'name' => 'title',
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),

                ),
                'validators' => array(
                    new StringLength(4, 255)
                )
            ),
            'code' => array(
                'name' => 'code',
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),

                ),
                'validators' => array(
                    new StringLength(4, 255)
                )
            ),
            'catId' => array(
                'name' => 'catId',
                'required' => false,
                'allow_empty' => true
            ),
            'note' => array(
                'name' => 'note',
                'filters' => array(
                    new Filter\StringTrim(),
                    new FilterHtml()
                )
            ),
        );
    }
}
