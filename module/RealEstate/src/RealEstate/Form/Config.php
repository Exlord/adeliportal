<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace RealEstate\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Config extends BaseForm
{
    private $userRoles;
    private $agentUserRole;
    private $Template;

    public function __construct($userRoles, $Template = array())
    {
        $this->Template = $Template;
        $this->userRoles = $userRoles;
        unset($userRoles[1]);
        unset($userRoles[2]);
        $this->agentUserRole = $userRoles;
        $this->setAttribute('class', 'normal-form ajax_submit');
        parent::__construct('real_estate_config');
        $this->setAttribute('action', url('admin/real-estate/config'));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'specialRealtyCost',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Special Realty cost',
            ),
            'attributes' => array(
                'class' => 'spinner withcomma'
            )
        ));

        $this->add(array(
            'name' => 'homeInfoCost',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Home Information Cost',
            ),
            'attributes' => array(
                'class' => 'spinner withcomma'
            )
        ));

        $this->add(array(
            'name' => 'showInfoPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'The price , display home owner  information',
            ),
            'attributes' => array(
                'class' => 'spinner withcomma'
            )
        ));

        $this->add(array(
            'name' => 'viewCounterMultiplier',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'View counter multiplier',
            ),
            'attributes' => array()
        ));

        /*$this->add(array(
            'name' => 'allAllowEdit',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Allow edit foe all guest :',
            ),
            'attributes' => array()
        ));*/


        $this->add(array(
            'name' => 'text4-sms-template',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'your message for Expiration estate',
                'empty_option' => '-- Select --',
                'value_options' => $this->Template
            ),
            'attributes' => array(
                'class'=>'select2',
            ),
        ));

        $this->add(array(
            'name' => 'agentUserRole',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Real-Estate Agent User Roles',
                'description' => 'Witch user roles will be treated as a Real-Estate Agent',
                'value_options' => $this->agentUserRole
            ),
            'attributes' => array(
                'class'=>'select2',
                'multiple' => 'multiple',
            )
        ));


        $images = new \Zend\Form\Fieldset('numberOfImages');
        $images->setLabel('Number of Images');

        foreach ($this->userRoles as $key => $role) {
            $images->add(array(
                'type' => 'Zend\Form\Element\Text',
                'name' => $key,
                'options' => array(
                    'label' => t($role),
                ),
                'attributes' => array(
                    'class' => 'spinner',
                    'data-min' => 0,
                    'data-step' => 1,
                    'data-max' => 20,
                    'value' => 1,
                )
            ));
        }
        $this->add($images);

        $this->add(new \System\Form\Buttons('real_estate_config'));
    }

    protected function addInputFilters()
    {
        // TODO: Implement addInputFilters() method.
        $filter = $this->getInputFilter();


        #filter by digit only
        $this->filterByDigit($filter, array(
            'showInfoPrice',
            'homeInfoCost',
            'specialRealtyCost',
        ));


        $this->setRequiredFalse($filter, array(
            'text4-sms-template',
        ));
    }
}
