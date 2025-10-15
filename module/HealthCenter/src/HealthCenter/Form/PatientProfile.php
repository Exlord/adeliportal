<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/30/14
 * Time: 10:54 AM
 */

namespace HealthCenter\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class PatientProfile extends BaseForm implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('PatientProfile');
        $this->setAttribute('class', 'ajax_submit');
    }

    protected function addElements()
    {
//        $this->add(array(
//            'name' => 'has_history',
//            'type' => 'Checkbox',
//            'options' => array(
//                'label' => 'HEALTH_CENTER_PATIENT_PROFILE_HAS_HISTORY',
//            )
//        ));
//
//        $this->add(array(
//            'name' => 'drug',
//            'type' => 'Textarea',
//            'options' => array(
//                'label' => 'HEALTH_CENTER_PATIENT_PROFILE_DRUG',
//            ),
//            'attributes' => array(
////                'cols' => 20,
//                'rows' => 5
//            )
//        ));
//
//        $relatives = new Fieldset('relatives');
//        $this->add($relatives);
//        $relatives->setLabel('Relatives');
//        $relatives->add(array(
//            'type' => 'Zend\Form\Element\Collection',
//            'name' => 'relatives',
//            'options' => array(
//                'label' => '',
//                'count' => 1,
//                'should_create_template' => true,
//                'allow_add' => true,
//                'target_element' => array(
//                    'type' => 'HealthCenter\Form\Element\Relatives'
//                ),
//            ),
//            'attributes' => array(
//                'class' => 'collection-container'
//            ),
//        ));
//
//        $relatives->add(array(
//            'name' => 'add_more_select_option',
//            'options' => array(
//                'label' => '',
//                'description' => '',
//            ),
//            'attributes' => array(
//                'type' => 'button',
//                'title' => t('Add More Select Options'),
//                'value' => t('Add More'),
//                'class' => 'btn btn-default add_collection_item',
//            ),
//        ));


    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return $this->inputFilters;
    }
}