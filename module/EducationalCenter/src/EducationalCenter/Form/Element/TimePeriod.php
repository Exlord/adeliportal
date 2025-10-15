<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/31/14
 * Time: 11:11 AM
 */

namespace EducationalCenter\Form\Element;


use Zend\Form\Fieldset;

class TimePeriod extends Fieldset
{
    public function __construct($name, $label)
    {
        parent::__construct($name);
        $this->setLabel($label);
        $this->setOption('twb-layout', 'inline');

        $hours = array();
        for ($i = 1; $i <= 12; $i++)
            $hours[$i] = $i;

        $minute = array();
        for ($i = 0; $i <= 59; $i++)
            $minute[$i] = $i;

        $this->add(array(
            'name' => 'hour',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Hour',
                'value_options' => $hours
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));
        $this->add(array(
            'name' => 'minute',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Minute',
                'value_options' => $minute
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));
        $this->add(array(
            'name' => 'part',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    'am' => 'A.M',
                    'pm' => 'P.M'
                )
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));
    }
} 