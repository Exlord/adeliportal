<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/27/14
 * Time: 12:38 PM
 */

namespace HealthCenter\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;

class TimeSearch extends BaseForm implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('time_search');
        $this->setAction(url('app/health-center'));
    }

    protected function addElements()
    {
        $doctorUserRoles = array();
        $config = getConfig('health-center');
        if (isset($config->varValue['doctorUserRole'])) {
            $doctorUserRoles = $config->varValue['doctorUserRole'];
        }
        $doctors = getSM('hc_doctor_table')->getDoctorsList($doctorUserRoles);
        $doctors = array('0' => '-- Select --') + $doctors;
        $this->add(array(
            'type' => 'Select',
            'name' => 'doctor',
            'options' => array(
                'label' => 'Doctor',
                'value_options' => $doctors
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $specs = getSM('category_item_table')->getItemsTreeByMachineName('doctor_specializations');
        $specs = array('0' => '-- Select --') + $specs;
        $this->add(array(
            'type' => 'Select',
            'name' => 'spec',
            'options' => array(
                'label' => 'Specialization',
                'value_options' => $specs
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'dateFrom',
            'options' => array(
                'label' => 'Date From',
                'add-on-prepend' => "<span class='show-date glyphicon glyphicon-calendar text-primary fa-lg'></span>",
            ),
            'attributes' => array(
                'class' => 'date disabled input-sm',
                'readonly' => 'readonly',
                'id' => 'date-start'
            )
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'dateTo',
            'options' => array(
                'label' => 'Date To',
                'add-on-prepend' => "<span class='show-date glyphicon glyphicon-calendar text-primary fa-lg'></span>",
            ),
            'attributes' => array(
                'class' => 'date disabled input-sm',
                'readonly' => 'readonly',
                'id' => 'date-end'
            )
        ));

        $startTime = array('0' => '-- Select --');
        for ($i = 1; $i <= 24; $i++)
            $startTime[$i] = $i;
        $this->add(array(
            'type' => 'Select',
            'name' => 'start',
            'options' => array(
                'label' => 'Start Time',
                'value_options' => $startTime
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'type' => 'Select',
            'name' => 'end',
            'options' => array(
                'label' => 'End Time',
                'value_options' => $startTime
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf__time_search',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600
                )
            )
        ));

        $this->add(array(
            'type' => 'System\Form\Element\SpamGuard',
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Search',
                'class' => 'btn btn-default btn-block btn-sm',
                'id' => 'search-button'
            ),
            'options' => array(
                'label' => 'Search',
                'glyphicon' => 'search',
//                'twb-layout' => 'inline'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'reset',
            'attributes' => array(
                'type' => 'button',
                'value' => 'Clear Form',
                'class' => 'btn btn-default btn-block btn-sm',
                'id' => 'reset-button'
            ),
            'options' => array(
                'label' => 'Clear Form',
                'glyphicon' => 'btn_cleaner_form',
//                'twb-layout' => 'inline'
            )
        ));
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