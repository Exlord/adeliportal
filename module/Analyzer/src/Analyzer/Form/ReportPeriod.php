<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Analyzer\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class ReportPeriod extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('report_period');
        $this->setLabel('Report Period');
        $this->attributes['class'] = 'inline-collection collection-item';

        $this->add(array(
            'name' => 'count',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-max' => 30,
                'data-step' => 1,
                'value' => 1
            )
        ));

        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => '',
                'description' => '',
                'value_options' => array(
                    'day' => 'Day',
                    'month' => 'Month',
                    'year' => 'Year'
                )
            ),
            'attributes' => array(
                'class'=>'select2'
            ),
        ));

        $this->add(array(
            'name' => 'label',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => '',
                'description' => '',
                'value_options' => array(
                    '0' => '-- Select Label --',
                    'date1' => 'Date1: mm/dd',
                    'date2' => 'Date2: yyyy/mm/dd',
                    'date3' => 'Date3: yyyy/mm',
                    'name' => 'Name (exp:today,sunday,january)',
                    'name_date1' => 'Name + Date1',
                    'name_date3' => 'Name + Date3',
                )
            ),
            'attributes' => array(
                'class'=>'select2',
            ),
        ));


        $this->add(array(
            'name' => 'drop_collection_item',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'value' => 'Delete This Item',
                'title' => t('Delete This Item'),
                'class' => 'button icon_button delete_button drop_collection_item',
            ),
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
        return array(
            'type' => array(
                'required' => false,
                'allow_empty' => true,
            ),
            'label' => array(
                'required' => false,
                'allow_empty' => true,
            )
        );
    }
}
