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

class Config extends BaseForm implements InputFilterProviderInterface
{
    private $userRoles;

    public function __construct()
    {
        $this->userRoles = getSM('role_table')->getRoleForSelect();
        unset($this->userRoles[1]);
        unset($this->userRoles[2]);
        $this->setAttribute('class', 'ajax_submit');
        $this->setAction(url('admin/health-center/config'));
        parent::__construct('hc_config');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'doctorUserRole',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Doctor/Counselor User Roles',
                'description' => 'Witch user roles will be treated as a Doctor/Counselor',
                'value_options' => $this->userRoles
            ),
            'attributes' => array(
                'class' => 'select2',
                'multiple' => 'multiple',
            )
        ));

        $pages = getSM('page_table')->getArray();
        $this->add(array(
            'name' => 'visitRules',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Visit Reservation Rules',
                'description' => '',
                'value_options' => $pages
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'sessionCost',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Each visit/consulting session cost',
                'description' => getCurrency()
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 1000,
                'data-step' => 1000
            )
        ));

        $this->add(array(
            'name' => 'reservationPaymentTimeout',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Reservation Payment Timeout (Minute)',
                'description' => 'the users reservation will be marked as failed if payment is not done with in the selected time after registration',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 1
            )
        ));

        $this->add(array(
            'name' => 'cancelTimeout',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Cancel Timeout(Hour)',
                'description' => 'how long before starting of a session, it can e canceled',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 1
            )
        ));

        //how long before a time can be canceled

//        if (getSM()->has('points_api')) {
//            $this->add(array(
//                'name' => 'reservePoint',
//                'type' => 'Zend\Form\Element\Text',
//                'options' => array(
//                    'label' => 'Reserve Points',
//                    'description' => 'how many points the user will earn for reserving a appointment'
//                ),
//                'attributes' => array(
//                    'class' => 'spinner',
//                    'data-min' => 1,
//                    'data-step' => 1
//                )
//            ));
//
//            $this->add(array(
//                'name' => 'reserveCancelPoint',
//                'type' => 'Zend\Form\Element\Text',
//                'options' => array(
//                    'label' => 'Reserve Cancel Points',
//                    'description' => 'how many points the user will loose for canceling a reserved appointment'
//                ),
//                'attributes' => array(
//                    'class' => 'spinner',
//                    'data-min' => 1,
//                    'data-step' => 1
//                )
//            ));
//        }

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