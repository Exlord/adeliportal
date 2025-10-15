<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace CustomersClub\Form;

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

class Point extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('cc_point');
        $this->setAttribute('class', 'ajax_submit');
        $this->setAttribute('data-cancel', url('admin/customers-club/points'));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                    '+' => '+',
                    '-' => '-'
                )
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'userId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'User',
                'value_options' => getSM('user_table')->getUsers(2)
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'points',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Point',
            ),
        ));

        $this->add(array(
            'name' => 'note',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Note',
            ),
        ));

        $this->add(new Buttons('cc_point'));
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
            'points' => array(
                'name' => 'points',
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    new Filter\Int()
                )
            ),
            'note' => array(
                'name' => 'note',
                'filters' => array(
                    new Filter\StringTrim(),
                    new Filter\StripTags()
                )
            )
        );
    }
}
