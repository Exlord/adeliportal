<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 5/26/13
 * Time: 10:02 AM
 */

namespace RealEstate\Form;


use System\Form\BaseForm;
use Zend\Filter\Digits;

class Filter extends BaseForm
{
    private $estateType;
    private $regType;
    private $stateId;
    private $cityId;
    private $prices;
    private $estateAreas;

    public function __construct($estateType, $regType, $stateId, $cityId, $prices, $estateAreas)
    {
        $this->estateType = $estateType;
        $this->regType = $regType;
        $this->stateId = $stateId;
        $this->cityId = $cityId;
        $this->prices = $prices;
        $this->estateAreas = $estateAreas;
        parent::__construct('estate_filter');
        $this->setAttribute('method', 'get');
    }

    protected function addElements()
    {
        /*$this->add(array(
            'name' => 'filter_isRequest',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Requested Estates',
            ),
            'attributes' => array(
                'value' => 0
            )
        ));*/

        $this->add(array(
            'name' => 'filter_estateType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Estate Type',
                'value_options' => $this->estateType,
            ),
            'attributes' => array(
                'value'=>'1',
                'class'=>'select2',
            )
        ));
        $this->add(array(
            'name' => 'filter_regType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Register Type',
                'value_options' => $this->regType,
            ),
            'attributes' => array(
                'value'=>'1',
                'class'=>'select2',
            )
        ));
        $this->add(array(
            'name' => 'filter_stateId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'State',
                'value_options' => $this->stateId,
                'empty_option' => '-- Select --',
            ),
            'attributes' => array(
                'id' => 'estate_filter_stateId',
                'data-cityid' => 'estate_filter_cityId',
                'class' => 'state-selector select2'
            )
        ));
        $this->add(array(
            'name' => 'filter_cityId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'City',
                'value_options' => $this->cityId,
                'empty_option' => '-- Select --',
            ),
            'attributes' => array(
                'id' => 'estate_filter_cityId',
                'class' => 'city-selector select2'
            )
        ));

        $this->add(array(
            'name' => 'filter_estateArea',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Estate Area',
                'empty_option' => '-- Select --',
                'value_options' => $this->estateAreas
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'filter_totalPrice_range',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Price',
                'value_options' => $this->prices,
                'empty_option' => '-- Select --',
            ),
            'attributes' => array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'filter_totalPrice_from',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Minimum Price',
            ),
            'attributes' => array(
                'class' => 'spinner ',
                'data-min' => 0,
                'data-step' => 10,
            )
        ));

        $this->add(array(
            'name' => 'filter_totalPrice_to',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Maximum Price',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 10,
            )
        ));

        $this->add(array(
            'name' => 'filter_mortgagePrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Maximum Mortgage Price',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'name' => 'filter_rentalPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Maximum Rental Price',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 10,
            )
        ));

        $this->add(array(
            'name' => 'search',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Search',
                'class' => 'button button_search',
            )
        ));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

       // $filter->get('filter_isRequest')->setRequired(false);
        $filter->get('filter_estateType')->setRequired(false);
        $filter->get('filter_regType')->setRequired(false);
        $filter->get('filter_stateId')->setRequired(false);
        $filter->get('filter_cityId')->setRequired(false);
        $filter->get('filter_estateArea')->setRequired(false);
        $filter->get('filter_totalPrice_range')->setRequired(false);

        $filter_totalPrice_from = $filter->get('filter_totalPrice_from');
        $filter_totalPrice_from->getFilterChain()->attach(new Digits());

        $filter_totalPrice_to = $filter->get('filter_totalPrice_to');
        $filter_totalPrice_to->getFilterChain()->attach(new Digits());

        $filter_mortgagePrice = $filter->get('filter_mortgagePrice');
        $filter_mortgagePrice->getFilterChain()->attach(new Digits());

        $filter_rentalPrice = $filter->get('filter_rentalPrice');
        $filter_rentalPrice->getFilterChain()->attach(new Digits());
    }
}