<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/1/13
 * Time: 11:04 AM
 */

namespace User\Form;


use Zend\Form\Fieldset;

class Profile extends Fieldset
{
    public function __construct($countryId = array(), $stateId = array(), $cityId = array())
    {
        parent::__construct('profile');
        $this->setLabel('General Details');

        $this->add(array(
            'name' => 'userId',
            'type' => 'Zend\Form\Element\Hidden',
            'options' => array(),
        ));
        $this->add(array(
            'name' => 'firstName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'First Name',
                'description' => '',
            ),
            'attributes' => array(
                'style' => 'max-width:250px;'
            )
        ));
        $this->add(array(
            'name' => 'lastName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Last Name',
                'description' => '',
            ),
            'attributes' => array(
                'style' => 'max-width:250px;'
            )
        ));

        $dateTitle = t('click here to select the date');
        $this->add(array(
            'name' => 'birthDate',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Birth Date',
                'description' => '',
                'add-on-prepend' => "<span class='show-date glyphicon glyphicon-calendar text-primary fa-lg' title='{$dateTitle}'></span>",
            ),
            'attributes' => array(
                'class' => 'left-align date disabled',
                'readonly' => 'readonly',
                'style' => 'max-width:206px;',
                'id' => 'birthDate'
            ),

        ));
        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Phone',
                'description' => '',
            ),
            'attributes' => array(
                'class' => 'left-align',
                'style' => 'max-width:250px;'
            ),

        ));
        $this->add(array(
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile',
                'description' => 'required for receiving sms from the system',
            ),
            'attributes' => array(
                'class' => 'left-align',
                'style' => 'max-width:250px;'
            ),

        ));
        $this->add(array(
            'name' => 'countryId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Country',
                'description' => '',
                'empty_option' => '-- Select --',
                'value_options' => $countryId,
            ),
            'attributes' => array(
                'data-stateid' => 'profile_stateId',
                'class' => 'country-selector select2',
                'style' => 'max-width:250px;'
            ),

        ));
        $this->add(array(
            'name' => 'stateId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'State',
                'description' => '',
                'empty_option' => '-- Select --',
                'value_options' => $stateId,
            ),
            'attributes' => array(
                'id' => 'profile_stateId',
                'data-cityid' => 'profile_cityId',
                'class' => 'state-selector select2',
                'style' => 'max-width:250px;'
            ),

        ));
        $this->add(array(
            'name' => 'cityId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'City',
                'description' => '',
                'value_options' => $cityId,
                'empty_option' => '-- Select --',
            ),
            'attributes' => array(
                'id' => 'profile_cityId',
                'class' => 'city-selector select2',
                'style' => 'max-width:250px;'
            ),

        ));
        $this->add(array(
            'name' => 'address',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Address',
                'description' => '',
            ),
            'attributes' => array(
                'style' => 'max-width:250px;'
            ),
        ));

        $this->add(array(
            'name' => 'aboutMe',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'About Me',
                'description' => '',
            ),
            'attributes' => array(
                'cols' => 50,
                'rows' => 3
            ),

        ));
    }
} 