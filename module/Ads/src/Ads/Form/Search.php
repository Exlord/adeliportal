<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/12/14
 * Time: 12:43 PM
 */

namespace Ads\Form;


use Components\Model\Block;
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
    private $baseType;
    private $isRequest;
    public $static_fields_data;
    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;

    public function __construct($block, $baseType, $isRequest)
    {
        $this->block = $block;
        $this->baseType = $baseType;
        $this->isRequest = $isRequest;
        $this->request = getSM('Request');
        parent::__construct('ad-search');
        $this->setAttribute('action', url('app/ad/list', array('baseType' => $this->baseType, 'isRequest' => $this->isRequest)));
        $this->setAttribute('method', 'GET');
    }

    protected function addElements()
    {
        $table = new Fieldset('table');
        $fieldset = new Fieldset('field');
        $this->add($table);

        $adTable = getSM('ads_table');
        $price_range = null;
        $states = null;
        $city_list = null;

        $type = $this->block->data[$this->block->type]['type'];
        foreach ($this->block->data[$type]['table'] as $field => $value) {
            if ($value != '0') {
                switch ($field) {
                    case 'stateId':
                        $states = getSM('state_table')->getArray(1);
                        $table->add(array(
                            'type' => 'Select',
                            'name' => $field,
                            'options' => array(
                                'label' => 'State',
                                'value_options' => $states,
                                'empty_option' => 'Select',
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
                                    'empty_option' => 'Select',
                                ),
                                'attributes' => array(
                                    'id' => 'cityId',
                                    'data-areaid' => 'areaId',
                                    'class' => 'city-selector select2'
                                )
                            ));
                        }
                        break;
                    case 'category':
                        $catLabel = 'Categories';
                        switch ($this->baseType) {
                            case 1000: // jobs
                            case 2000: //resume
                                $catLabel = 'ADS_ACTIVITIES';
                                break;
                            case 3000: //ads
                                $catLabel = 'Categories';
                                break;
                            case 4000: // realEstate
                            case 5000: // cars
                                $catLabel = 'Categories';
                                break;
                        }
                        $table->add(array(
                            'type' => 'Text',
                            'name' => $field,
                            'options' => array(
                                'label' => $catLabel,
                            ),
                            'attributes' => array(
                                'class' => ''
                            )
                        ));
                        break;
                    case 'title':
                        $table->add(array(
                            'type' => 'Text',
                            'name' => $field,
                            'options' => array(
                                'label' => 'Title',
                            ),
                            'attributes' => array(
                                'class' => ''
                            )
                        ));
                        break;
                    case 'address':
                        $table->add(array(
                            'type' => 'Text',
                            'name' => $field,
                            'options' => array(
                                'label' => 'Address',
                            ),
                            'attributes' => array(
                                'class' => ''
                            )
                        ));
                        break;
                    case 'name':
                        $table->add(array(
                            'type' => 'Text',
                            'name' => $field,
                            'options' => array(
                                'label' => 'Name',
                            ),
                            'attributes' => array(
                                'class' => ''
                            )
                        ));
                        break;
                    case 'mobile':
                        $table->add(array(
                            'type' => 'Text',
                            'name' => $field,
                            'options' => array(
                                'label' => 'Mobile',
                            ),
                            'attributes' => array(
                                'class' => ''
                            )
                        ));
                        break;
                }
            }
        }
        if (isset($this->block->data[$type]['field'])) {
            $fieldIds = array();
            $fieldsForRange = array();
            $fields = array();
            $fieldsSelect = array();
            $counter = 0;
            foreach ($this->block->data[$type]['field'] as $field => $value) {
                if ($value != '0') {
                    $counter++;
                    $fieldParts = explode(',', $field);
                    $fieldIds[] = $fieldParts[0];
                    switch ($value) {
                        case 'spinner':
                        case 'slider':
                            $fieldsForRange[] = $fieldParts[1];
                            $fields[$fieldParts[1]] = $value;
                            break;
                        case 'select':
                            $fieldsForRange[] = $fieldParts[1];
                            $fieldsSelect[$fieldParts[1]] = $value;
                    }
                }
            }


            /* @var $fieldsApi \Fields\API\Fields */
            $fieldsApi = getSM()->get('fields_api');
            $fieldsTable = $fieldsApi->init('ads_' . $this->baseType . '_' . $this->isRequest);
            $fieldsRange = $adTable->getFieldsRangeValue($fieldsTable, $fieldsForRange, $this->baseType);

            $data_list = getSM('fields_table')->getById($fieldIds);
            $data_list_get = array();
            foreach ($data_list as $row) {
                $fieldMachineName = $row->fieldMachineName;

                if (in_array($fieldMachineName, array_keys($fieldsSelect))) {
                    $keyName = $row->id . ',' . $fieldMachineName . '_counter';
                    $f_counter = 0;
                    if (isset($this->block->data[$type]['field'][$keyName]))
                        $f_counter = $this->block->data[$type]['field'][$keyName];
                    if ($f_counter) {
                        $max = 0;
                        if (isset($fieldsRange['max_' . $fieldMachineName]))
                            $max = $fieldsRange['max_' . $fieldMachineName];

                        if ($max) {
                            $i = 1;
                            $minKey = 0;
                            $maxKey = $f_counter;
                            $valueSelect = array();
                            $valueSelect[] = array(
                                'field_key' => '',
                                'field_value' => t('None'),
                            );

                            while ($i > 0) {
                                $valueSelect[] = array(
                                    'field_key' => $minKey . ',' . $maxKey,
                                    'field_value' => $this->insertComma($minKey) . ' ' . t('APP_TO') . ' ' . $this->insertComma($maxKey),
                                );
                                $minKey += $f_counter;
                                $maxKey += $f_counter;
                                if ($maxKey > $max)
                                    $i = -1;
                            }


                            $row->fieldType = 'select';
                            $configData = array(
                                'select_field' => array(
                                    'select_field_size' => 1,
                                    'select_field_select_count' => 1,
                                    'keyValuePairs' => $valueSelect,
                                )
                            );
                            $row->fieldConfigData = serialize($configData);
                        }
                    }
                }
                $data_list_get[] = $row;
            }

            if ($fieldset) {
                $fields_set = $fieldset;
            } else
                $fields_set = $this;
            $fieldsApi->loadFields($data_list_get, $fields_set);


            foreach ($fields as $name => $type) {

                $fieldset->get($name)->setAttributes(array(
                    'id' => $name,
                    'class' => $type,
                    'data-min' => 0,
                    'data-max' => $fieldsRange['max_' . $name],
                    'data-step' => 1,
                ));
            }

            if ($counter > 0) {
                $fieldset->setAttributes(array(
                    'class' => 'f_dynamic',
                ));
                $this->add($fieldset);
            }

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

    private function insertComma($st)
    {
        $i = 1;
        $count = 3;
        $first = strlen($st) - 3;
        $x = '';
        if (strlen($st) > $count) {
            while ($i > 0) {
                $y = substr($st, $first, $count);
                if ($x)
                    $y = $y . ',';
                $x = $y . $x;
                $first = $first - 3;
                if ($first < 0) {
                    if ($first + 3 > 0){
                        $count = $first + 3;
                        $first = 0;
                    }
                    else
                        $i = -1;
                }
            }
        } else
            $x = $st;
        return $x;
    }
}