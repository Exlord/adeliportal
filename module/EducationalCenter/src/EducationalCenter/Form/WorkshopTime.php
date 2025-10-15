<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/31/14
 * Time: 11:03 AM
 */

namespace EducationalCenter\Form;


use EducationalCenter\Form\Element\TimePeriod;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\InputFilter\InputFilterProviderInterface;

class WorkshopTime extends BaseForm implements InputFilterProviderInterface
{
    private $type;

    public function __construct($type)
    {
        $this->type = $type;
        parent::__construct('workshop_time');
        $this->setAttribute('class', 'row ajax_submit');
    }

    protected function addElements()
    {
        $dateTitle = t('click here to select the date');
        if ($this->type == 'single-day') {
            $this->add(array(
                'name' => 'date',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Date',
                    'add-on-prepend' => "<span class='show-date glyphicon glyphicon-calendar text-primary fa-lg' title='{$dateTitle}'></span>",
                ),
                'attributes' => array(
                    'class' => 'date disabled',
                    'readonly' => 'readonly',
                    'id' => 'date'
                )
            ));
        } elseif ($this->type == 'periodic') {
            $this->add(array(
                'name' => 'date-start',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Start Date',
                    'add-on-prepend' => "<span class='show-date glyphicon glyphicon-calendar text-primary fa-lg' title='{$dateTitle}'></span>",
                ),
                'attributes' => array(
                    'class' => 'date disabled',
                    'readonly' => 'readonly',
                    'id' => 'date-start'
                ),
                'column-size' => 'md-6',
            ));

            $this->add(array(
                'name' => 'date-end',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'End Date',
                    'add-on-prepend' => "<span class='show-date glyphicon glyphicon-calendar text-primary fa-lg' title='{$dateTitle}'></span>",
                ),
                'attributes' => array(
                    'class' => 'date disabled',
                    'readonly' => 'readonly',
                    'id' => 'date-end'
                ),
                'column-size' => 'md-6',
            ));

            $this->add(array(
                'name' => 'days',
                'type' => 'Zend\Form\Element\MultiCheckbox',
                'options' => array(
                    'label' => 'Day of the Week',
                    'value_options' => array(
                        6 => 'Saturday',
                        7 => 'Sunday',
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                    ),
                ),
                'attributes' => array(
                    'id' => 'days_of_week'
                ),
            ));
        }

        $start = new TimePeriod('start', 'Start Time');
        $start->setAttribute('class', 'col-md-6');
        $this->add($start);

        $end = new TimePeriod('end', 'End Time');
        $end->setAttribute('class', 'col-md-6');
        $this->add($end);

        $buttons = new Buttons('workshop_time');
        $buttons->setAttribute('class', 'clearfix col-xs-12');
        $this->add($buttons);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $input = array();
        if ($this->type == 'single-day') {
            $input['date'] = array(
                'name' => 'date',
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    new StringTrim(),
//                    new StripTags()
                )
            );

        } elseif ($this->type == 'periodic') {
            $input['date-start'] = array(
                'name' => 'date-start',
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    new StringTrim(),
                    new StripTags()
                )
            );
            $input['date-end'] = array(
                'name' => 'date-end',
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    new StringTrim(),
                    new StripTags()
                )
            );
        }
        return $input;
    }
}