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

class WorkshopClass extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('workshop_class');
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

        $this->add(array(
            'name' => 'educatorId',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Educator'
            ),
            'attributes' => array(
                'placeholder' => 'Educator',
                'id' => 'educator'
            )
        ));

        $this->add(array(
            'name' => 'capacity',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Capacity'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'min' => 1,
                'max' => 999,
                'step' => 1
            )
        ));

        $this->add(array(
            'name' => 'price',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'price',
                'add-on-append' => t(getCurrency()),
            ),
            'attributes' => array(
                'class' => 'num',
            )
        ));

        $this->add(array(
            'name' => 'location',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Location',
                'description' => 'Workshop class venue'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Phone',
                'description' => 'Workshop class venue phone'
            ),
            'attributes' => array()
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
            'catId' => array(
                'name' => 'catId',
                'required' => false,
                'allow_empty' => true
            ),
            'capacity' => array(
                'name' => 'capacity',
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                    new Filter\Digits(),
                ),
                'validators' => array(
                    new StringLength(1, 3),
                )
            ),
            'price' => array(
                'name' => 'price',
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                    new Filter\Digits(),
                ),
                'validators' => array(
                    new StringLength(1, 15),
                )
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
