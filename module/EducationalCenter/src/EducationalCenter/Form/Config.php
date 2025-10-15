<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/27/14
 * Time: 11:40 AM
 */

namespace EducationalCenter\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\InputFilter\InputFilterProviderInterface;

class Config extends BaseForm implements InputFilterProviderInterface
{
    private $userRoles;

    public function __construct()
    {
        $this->userRoles = getSM('role_table')->getRoleForSelect();
        unset($this->userRoles[1]);
        unset($this->userRoles[2]);
        $this->setAttribute('class', 'ajax_submit');
        $this->setAction(url('admin/educational-center/config'));
        parent::__construct('ec_config');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'educatorUserRole',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Educator User Roles',
                'description' => 'Witch user roles will be treated as a Educator',
                'value_options' => $this->userRoles
            ),
            'attributes' => array(
                'class' => 'select2',
                'multiple' => 'multiple',
            )
        ));

        $pages = getSM('page_table')->getArray();
        $this->add(array(
            'name' => 'workshopClassRules',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Workshop Class Rules',
                'description' => '',
                'value_options' => $pages
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'classRegisterTimeout',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Class Register Timeout(Hour)',
                'description' => 'how long before a class start date users can register for that class',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 1
            )
        ));

        $this->add(array(
            'name' => 'classPaymentTimeout',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Class Payment Timeout (Minute)',
                'description' => 'the users class registration will be marked as failed if payment is not done with in the selected time after registration',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 1
            )
        ));

        $this->add(array(
            'name' => 'notifyTime',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Notify Time(Hour)',
                'description' => 'how long before class start date, attendance should be notified',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 1
            )
        ));

        $this->add(array(
            'name' => 'classCancelTimeout',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Class Cancel Timeout (Hour)',
                'description' => 'how long before class start date, it can be canceled',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 1
            )
        ));

        //how long before a time can be canceled
        //how long before a canceled time can be uncanceled

        $this->add(new Buttons('ec_config'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array();
    }
}