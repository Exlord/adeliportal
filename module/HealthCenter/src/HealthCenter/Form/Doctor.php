<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/27/14
 * Time: 11:40 AM
 */

namespace HealthCenter\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Filter\Digits;
use Zend\Filter\Int;
use Zend\Filter\StringTrim;
use Zend\InputFilter\InputFilterProviderInterface;

class Doctor extends BaseForm implements InputFilterProviderInterface
{
    private $userRoles;

    public function __construct()
    {
        $this->setAttribute('class', 'ajax_submit');
        parent::__construct('hc_doctor_profile');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'specializations',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Doctor/Counselor Specializations',
                'description' => '',
                'value_options' => getSM('category_item_table')->getItemsTreeByMachineName('doctor_specializations')
            ),
            'attributes' => array(
                'class' => 'select2',
                'multiple' => 'multiple',
            )
        ));

        $this->add(array(
            'name' => 'sessionCost',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Each visit/consulting session cost',
                'description' => getCurrency() . ', ' . t('if 0 or empty global value will be used')
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 1000,
                'data-step' => 1000
            )
        ));

        $this->add(new Buttons('hc_config'));
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
            'sessionCost' => array(
                'name' => 'sessionCost',
                'filters' => array(
                    new StringTrim(),
                    new Digits(),
                    new Int()
                )
            )
        );
    }
}