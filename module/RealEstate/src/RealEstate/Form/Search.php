<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/12/14
 * Time: 12:43 PM
 */

namespace RealEstate\Form;


use Components\Model\Block;
use RealEstate\Model\RealEstateTable;
use System\Form\BaseForm;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class Search extends BaseForm implements InputFilterProviderInterface
{
    protected $loadInputFilters = false;
    /**
     * @var Block
     */
    private $block;
    public $static_fields_data;
    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;

    public function __construct($block)
    {
        $this->block = $block;
        $this->request = getSM('Request');
        parent::__construct('real-estate-search');
        $this->setAttribute('action', url('app/real-estate/list'));
        $this->setAttribute('method', 'GET');
    }

    protected function addElements()
    {
        $table = new Fieldset('table');
        $fieldset = new Fieldset('field');
        $this->add($table);
        $this->add($fieldset);

        /* @var $realestateTable RealEstateTable */
        $realestateTable = getSM('real_estate_table');
        $price_range = null;
        $states = null;
        $city_list = null;
        $area_list = null;

        foreach ($this->block->data['real_estate_search_block']['table'] as $field => $value) {
            if ($value != '0') {
                switch ($field) {
                    case 'estateType':
                        $this->static_fields_data['estateType'] = getSM()->get('category_item_table')->getItemsTreeByMachineName('estate_type');
                        switch ($value) {
                            case 'select':
                                $table->add(array(
                                    'type' => 'Select',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Estate Type',
                                        'value_options' => $this->static_fields_data['estateType'],
                                    ),
                                    'attributes' => array(
                                        'class' => 'select2'
                                    )
                                ));
                                break;
                            case 'multi-select':
                                $table->add(array(
                                    'type' => 'Select',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Estate Type',
                                        'value_options' => $this->static_fields_data['estateType'],
                                    ),
                                    'attributes' => array(
                                        'class' => 'select2',
                                        'multiple' => 'multiple'
                                    )
                                ));
                                break;
                            case 'checkbox':
                                $table->add(array(
                                    'type' => 'MultiCheckbox',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Estate Type',
                                        'value_options' => $this->static_fields_data['estateType'],
                                    ),
                                    'attributes' => array()
                                ));
                                break;
                            case 'radio':
                                $table->add(array(
                                    'type' => 'Radio',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Estate Type',
                                        'value_options' => $this->static_fields_data['estateType'],
                                    ),
                                    'attributes' => array()
                                ));
                                break;
                        }
                        break;
                    case 'regType':
                        $regType = getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');
                        $this->static_fields_data['regType'] = $regType;

                        switch ($value) {
                            case 'select':
                                $table->add(array(
                                    'type' => 'Select',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Register Type',
                                        'value_options' => $this->static_fields_data['regType'],
                                    ),
                                    'attributes' => array(
                                        'class' => 'select2'
                                    )
                                ));
                                break;
                            case 'multi-select':
                                $table->add(array(
                                    'type' => 'Select',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Register Type',
                                        'value_options' => $this->static_fields_data['regType'],
                                    ),
                                    'attributes' => array(
                                        'class' => 'select2',
                                        'multiple' => 'multiple'
                                    )
                                ));
                                break;
                            case 'checkbox':
                                $table->add(array(
                                    'type' => 'MultiCheckbox',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Register Type',
                                        'value_options' => $this->static_fields_data['regType'],
                                    ),
                                    'attributes' => array()
                                ));
                                break;
                            case 'radio':
                                $table->add(array(
                                    'type' => 'Radio',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Register Type',
                                        'value_options' => $this->static_fields_data['regType'],
                                    ),
                                    'attributes' => array()
                                ));
                                break;
                        }
                        break;
                    case 'isRequest':
                        $this->static_fields_data['isRequest'] = \RealEstate\Model\RealEstateTable::$isRequest;
                        switch ($value) {
                            case 'select':
                                $table->add(array(
                                    'type' => 'Select',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Type',
                                        'value_options' => $this->static_fields_data['isRequest'],
                                    ),
                                    'attributes' => array(
                                        'class' => 'select2'
                                    )
                                ));
                                break;
                            case 'multi-select':
                                $table->add(array(
                                    'type' => 'Select',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Type',
                                        'value_options' => $this->static_fields_data['isRequest'],
                                    ),
                                    'attributes' => array(
                                        'class' => 'select2',
                                        'multiple' => 'multiple'
                                    )
                                ));
                                break;
                            case 'checkbox':
                                $table->add(array(
                                    'type' => 'MultiCheckbox',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Type',
                                        'value_options' => $this->static_fields_data['isRequest'],
                                    ),
                                    'attributes' => array()
                                ));
                                break;
                            case 'radio':
                                $table->add(array(
                                    'type' => 'Radio',
                                    'name' => $field,
                                    'options' => array(
                                        'label' => 'Type',
                                        'value_options' => $this->static_fields_data['isRequest'],
                                    ),
                                    'attributes' => array()
                                ));
                                break;
                        }
                        break;
                    case 'stateId':
                        $states = getSM('state_table')->getArray(1);
                        $table->add(array(
                            'type' => 'Select',
                            'name' => $field,
                            'options' => array(
                                'label' => 'State',
                                'value_options' => $states,
                                'empty_option' => '-- Select --',
                            ),
                            'attributes' => array(
                                'id' => 'stateId',
                                'data-cityid' => 'cityId',
                                'class' => 'state-selector select2'
                            )
                        ));
                        break;
                    case 'cityId':
                        if (!is_null($states)) {
                            $query = $this->request->getQuery('table', array());
                            $selected_state = (isset($query['stateId']) && !empty($query['stateId'])) ? $query['stateId'] : current(array_keys($states));
                            $city_list = getSM()->get('city_table')->getArray($selected_state);
                            $table->add(array(
                                'type' => 'Select',
                                'name' => $field,
                                'options' => array(
                                    'label' => 'City',
                                    'value_options' => $city_list,
                                    'empty_option' => '-- Select --',
                                ),
                                'attributes' => array(
                                    'id' => 'cityId',
                                    'data-areaid' => 'areaId',
                                    'class' => 'city-selector select2'
                                )
                            ));
                        }
                        break;
                    case 'parentAreaId':
                        if (!is_null($city_list)) {
                            $query = $this->request->getQuery('table', array());
                            $selected_city = (isset($query['cityId']) && !empty($query['cityId'])) ? $query['cityId'] : current(array_keys($city_list));
                            $area_list = getSM()->get('city_area_table')->getArray($selected_city, 0);
                            $table->add(array(
                                'type' => 'Select',
                                'name' => $field,
                                'options' => array(
                                    'label' => 'Region',
                                    'value_options' => $area_list,
                                    'empty_option' => '-- Select --',
                                ),
                                'attributes' => array(
                                    'id' => 'parentAreaId',
                                    'data-areaid' => 'areaId',
                                    'class' => 'area-selector select2'
                                )
                            ));
                        }
                        break;
                    case 'areaId':
                        if (!is_null($area_list && $city_list)) {
                            $query = $this->request->getQuery('table', array());
                            $selected_area = (isset($query['parentAreaId']) && !empty($query['parentAreaId'])) ? $query['parentAreaId'] : current(array_keys($area_list));
                            $selected_city = (isset($query['cityId']) && !empty($query['cityId'])) ? $query['cityId'] : current(array_keys($city_list));
                            $sub_area_list = getSM()->get('city_area_table')->getArray($selected_city, $selected_area);
                            $table->add(array(
                                'type' => 'Select',
                                'name' => $field,
                                'options' => array(
                                    'label' => 'REALESTATE_SUB_REGION',
                                    'value_options' => $sub_area_list,
                                    'empty_option' => '-- Select --',
                                ),
                                'attributes' => array(
                                    'id' => 'areaId',
                                    'class' => 'sub-area-selector select2'
                                )
                            ));
                        }
                        break;
                    case 'userId':
                        $this->static_fields_data['userId'] = array();
                        $agentUserRole = getConfig('real_estate_config')->varValue;
                        if (isset($agentUserRole['agentUserRole']) && $agentUserRole['agentUserRole']) {
                            $agentUserRole = $agentUserRole['agentUserRole'];
                            $select = getSM('user_table')->getByRoleId($agentUserRole, false, 'full', 1);
                            if ($select)
                                foreach ($select as $row)
                                    $this->static_fields_data['userId'][$row->id] = $row->displayName;
                        }
                        $table->add(array(
                            'type' => 'Select',
                            'name' => $field,
                            'options' => array(
                                'label' => 'REALESTATE_AGENT',
                                'value_options' => $this->static_fields_data['userId'],
                                'empty_option' => '-- Select --'
                            ),
                            'attributes' => array(
                                'class' => 'select2'
                            )
                        ));
                        break;
                    case 'totalPrice':

                        if (is_null($price_range))
                            $price_range = $realestateTable->getPriceRange();
                        $table->add(array(
                            'type' => 'Text',
                            'name' => $field,
                            'options' => array(
                                'label' => 'Price',
                            ),
                            'attributes' => array(
                                'id' => 'totalPrice',
                                'class' => 'slider',
                                'data-min' => $price_range['min_totalPrice'],
                                'data-max' => $price_range['max_totalPrice'],
                                'data-step' => 100000
                            )
                        ));
                        break;
                    case 'mortgagePrice':
                        if (is_null($price_range))
                            $price_range = $realestateTable->getPriceRange();
                        $table->add(array(
                            'type' => 'Text',
                            'name' => $field,
                            'options' => array(
                                'label' => 'Mortgage Price',
                            ),
                            'attributes' => array(
                                'id' => 'mortgagePrice',
                                'class' => 'slider',
                                'data-min' => $price_range['min_mortgagePrice'],
                                'data-max' => $price_range['max_mortgagePrice'],
                                'data-step' => 100000
                            )
                        ));
                        break;
                    case 'rentalPrice':
                        if (is_null($price_range))
                            $price_range = $realestateTable->getPriceRange();
                        $table->add(array(
                            'type' => 'Text',
                            'name' => $field,
                            'options' => array(
                                'label' => 'Rental Price',
                            ),
                            'attributes' => array(
                                'id' => 'rentalPrice',
                                'class' => 'slider',
                                'data-min' => $price_range['min_rentalPrice'],
                                'data-max' => $price_range['max_rentalPrice'],
                                'data-step' => 100000,
                            )
                        ));
                        break;
//                    case 'estateArea':
//                        $estateArea = getSM('real_estate_table')->getAreas();
//                        $this->static_fields_data['estateArea'] = $estateArea;
//                        switch ($value) {
//                            case 'select':
//                                $list = array();
//                                $min = 0;
//                                $start = 0;
//                                $max = array_pop($estateArea);
//                                for ($i = 0; $i < ceil($max / 50); $i++) {
//                                    if ($start + 49 < $max) {
//                                        $nextKey = ',' . ($start + 49);
//                                        $nextVal = ' - ' . ($start + 49);
//                                    } else {
//                                        $nextKey = '';
//                                        $nextVal = ' +';
//                                    }
//                                    $list[$start . $nextKey] = $start . $nextVal;
//                                    $start += 49;
//                                }
//                                $this->add(array(
//                                    'type' => 'Select',
//                                    'name' => $field,
//                                    'options' => array(
//                                        'label' => 'Estate Type',
//                                        'value_options' => $list,
//                                    ),
//                                    'attributes' => array(
//                                        'class' => 'select2'
//                                    )
//                                ));
//                                break;
//                        }
//
//                        break;
                }
            }
        }

        $fieldIds = array();
        $fieldsForRange = array();
        $fields = array();
        foreach ($this->block->data['real_estate_search_block']['field'] as $field => $value) {
            if ($value != '0') {
                $fieldParts = explode(',', $field);
                $fieldIds[] = $fieldParts[0];
                switch ($value) {
                    case 'spinner':
                    case 'slider':
                        $fieldsForRange[] = $fieldParts[1];
                        $fields[$fieldParts[1]] = $value;
                        break;
                }
            }
        }

        /* @var $fieldsApi \Fields\API\Fields */
        $fieldsApi = getSM()->get('fields_api');
        $fieldsTable = $fieldsApi->init('real_estate');
        $fieldsApi->loadFieldsById($fieldIds, $this, $fieldset);

        $fieldsRange = $realestateTable->getFieldsRangeValue($fieldsTable, $fieldsForRange);
        foreach ($fields as $name => $type) {

            $fieldset->get($name)->setAttributes(array(
                'id' => $name,
                'class' => $type,
                'data-min' => $fieldsRange['min_' . $name],
                'data-max' => $fieldsRange['max_' . $name],
                'data-step' => 1,
            ));
        }

        $this->add(array(
            'type' => 'Button',
            'name' => 'search',
            'options' => array(
                'glyphicon' => 'search text-primary fa-lg',
                'label' => 'Search'
            ),
            'attributes' => array(
                'value' => 'Search',
                'class' => 'btn btn-default',
                'type' => 'submit'
            )
        ));
    }

    /**
     * @return \Zend\Http\PhpEnvironment\Request
     */
    private function getRequest()
    {
        return $this->request;
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $filter = array();
        foreach ($this->getFieldsets() as $fieldset) {
            foreach ($this->getElements() as $el) {
                $filter[$fieldset->getName()][$el->getName()] = array(
                    'name' => $el->getName(),
                    'required' => false,
                    'allow_empty' => true
                );
            }
        }

        return $filter;
    }
}