<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Fields\API;

use Application\API\App;
use Fields\Form\Element\Barcode;
use Fields\Form\Element\CurrentDate;
use Fields\Form\Element\TargetElement;
use Fields\Form\Element\UniqueCode;
use Fields\Model\Field;
use File\API\File;
use System\API\BaseAPI;
use System\DB\BaseTableGateway;
use Theme\API\Common;
use Theme\API\Table;
use Theme\API\Themes;
use TwbBundle\Form\Element\StaticElement;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Factory;
use Zend\Form\Fieldset;
use Zend\Form\Form;

class Fields extends BaseAPI
{
    private $table_cache_key;
    private $cache_item;
    private $entityType;
    /**
     * @var Adapter
     */
    private $adapter;
    private $table_name;
    /**
     * @var \Fields\Model\FieldTable
     */
    private $fields_table;
    private $formFactory = null;
    private $collectionFields = array();

    public $inputFilterConfig = array();

    public $hasFileUploadField = false;
    public $fileUploadFields = array();

    public $hasColorField = false;
    public $hasCollectionField = false;

    public function init($entityType)
    {
        $this->fields_table = $this->getServiceLocator()->get('fields_table');
        $this->adapter = $this->fields_table->getAdapter();
        $this->table_name = $this->makeTableName($entityType);
        $this->table_cache_key = 'table_name_' . $this->table_name;
        $this->entityType = $entityType;
        $this->__checkTable();
        $this->__checkColumns();
        return $this->table_name;
    }

    public function generate($fields, $data, $olfFields = false)
    {
//        $markup = '';
//        $temp_wrapper = "<div class='field %s'>%s</div>";
//        $default_temp = "<span class='field_label'>LABEL</span><span class='field_value'> FIELD</span>";
        $default_temp = "FIELD";
        $output = array();
        /* @var $field \Fields\Model\Field */
        foreach ($fields as $field) {

            $label = $field['fieldName'];
            $preFix = $field['fieldPrefix'];
            $postFix = $field['fieldPostfix'];
            $mName = $field['fieldMachineName'];

            if (isset($data[$mName])) {

                if (!empty($preFix))
                    $label = $preFix;

                $temp = !empty($field['fieldDisplayTemplate']) ? $field['fieldDisplayTemplate'] : $default_temp;
//            $text = '';

                if (is_array($field['fieldConfigData']))
                    $fieldConfigData = $field['fieldConfigData'];
                else {
                    $field['fieldConfigData'] = $fieldConfigData = unserialize($field['fieldConfigData']);
                }


                $fieldType = $field['fieldType'];
                switch ($fieldType) {
                    case 'text':
                        if (has_value($data[$mName])) {
                            if ($fieldConfigData['text_field']['text_type'] == 'text_web')
                                $output[$label] = @unserialize($data[$mName]);
                            elseif ($fieldConfigData['text_field']['text_type'] == 'color')
                                $output[$label] = "<span style='background:#{$data[$mName]};' class='color-picker-display'>&nbsp;</span>";
                            else
                                $output[$label] = str_replace('FIELD', $data[$mName], $temp);
                        }
                        break;
                    case 'long_text':
                        if (has_value($data[$mName])) {
                            $output[$label] = str_replace('FIELD', $data[$mName], $temp);
                        }
                        break;
                    case 'checkBox':
                    case 'radio':
                    case 'select':
                        if (has_value($data[$mName])) {
                            $keyValuePairs = $fieldConfigData[$field['fieldType'] . '_field']['keyValuePairs'];
//                            if (isset($keyValuePairs[SYSTEM_LANG]))
//                                $keyValuePairs = $keyValuePairs[SYSTEM_LANG];
//                            else {
//                                if (!is_numeric(current(array_keys($keyValuePairs)))) {
//                                    $keyValuePairs = current($keyValuePairs);
//                                }
//                            }

                            //for select and multicheckbox
                            if (count($keyValuePairs) > 1 && $fieldType != 'radio') {
                                $values = @unserialize($data[$mName]); //it is possible that only 1 checkbox is checked therefor this value is a simple string and not a serialized array
                                if (!$values)
                                    $values = array($data[$mName]);


                                $field_value = array();
                                foreach ($keyValuePairs as $value) {
                                    if (in_array($value['field_key'], $values)) {

                                        $field_value[] = $value['field_value'];
                                    }

                                }
                                $field_value = implode(', ', $field_value);

                            } elseif ($fieldType == 'radio') {
                                $value = $data[$mName];
                                foreach ($keyValuePairs as $values) {
                                    if ($value === $values['field_key']) {
                                        $field_value = $values['field_value'];
                                        break;
                                    }
                                }
                            } else { //this is for single checkbox only
                                $field_value = current($keyValuePairs);
                                if ($field_value['field_key'] === $data[$mName])
                                    $field_value = $field_value['field_value'];
                                else
                                    $field_value = '';
                            }
                            if (!empty($field_value)) {
//                            $text = str_replace('LABEL', $label, $temp);
//                            $text = str_replace('FIELD', $field_value, $text);
                                $output[$label] = str_replace('FIELD', $field_value, $temp);
                            }
                        }
                        break;
                    case 'fileUpload':
                        if (has_value($data[$mName])) {
                            if (!is_array($data[$mName]))
                                $data[$mName] = @unserialize($data[$mName]);
                            $output[$label] = $data[$mName];
                        }
                        break;
                    case 'collection':
                        if (!is_array($data[$mName]))
                            $data[$mName] = @unserialize($data[$mName]);
                        $output[$label] = self::RenderCollection($data[$mName], $field);
                        break;
//                    case 'constant_field':
//                        break
                    default:

                }
            } elseif ($olfFields)
                $output[$label] = '';
//            if (!empty($text)) {
//                $text = sprintf($temp_wrapper, '', $text);
//                $markup .= $text;
//            }

        }
        return $output;
    }

    public function loadFields($fields, $fields_set, &$inputFilterConfig = null, $forEdit = false)
    {
        if (!is_array($inputFilterConfig))
            $this->inputFilterConfig = array();

        /* @var $field \Fields\Model\Field */
        foreach ($fields as $field) {

            /* @var $el \Zend\Form\Element */
            $el = null;

            $name = $field->getFieldMachineName();
            $configData = unserialize($field->getFieldConfigData());
            $field->validators = unserialize($field->validators);
            $field->filters = unserialize($field->filters);
            $label = $field->getFieldName();
            $preFix = $field->fieldPrefix;
            $postFix = $field->fieldPostfix;
            $isFieldset = false;
            if (!empty($preFix))
                $label = $preFix;

            //region Validators and filters
            //------------------------------------------------------validations and filters
            //get required value and remove it from validators
            $required = false;
            if (isset($field->validators['required'])) {
                $required = ($field->validators['required'] == '1');
                unset($field->validators['required']);
            }

            //get allow_empty and remove it from validators
            $allow_empty = true;
            if (isset($field->validators['allow_empty'])) {
                $allow_empty = ($field->validators['allow_empty'] == '0');
                unset($field->validators['allow_empty']);
            }

            $setFilters = true;

            if ($field->getFieldType() == 'fileUpload')
                $setFilters = false;
            elseif ($field->getFieldType() == 'text' && $configData['text_field']['text_type'] == 'text_web')
                $setFilters = false;

            if ($setFilters) {
                $inputFilters = $this->_makeInputFilters($field->getFieldMachineName(), $required, $allow_empty, $field->validators, $field->filters);

                if (!is_array($inputFilterConfig))
                    $this->inputFilterConfig[$field->getFieldMachineName()] = $inputFilters;
                else
                    $inputFilterConfig[$field->getFieldMachineName()] = $inputFilters;
            }
            //endregion

            switch ($field->getFieldType()) {
                case 'text':
                    $text_type = $configData['text_field']['text_type'];
                    if ($text_type != 'text_web') {
                        $el = new Element\Text();
                        $el->setAttributes(array(
                            'size' => $configData['text_field']['text_field_size'],
                            'maxLength' => $configData['text_field']['text_field_max_length'],
                            'placeholder' => $configData['text_field']['text_field_watermark'],
                        ));
                        if ($text_type == 'color') {
                            $el->setAttribute('data-type', 'color');
                            $el->setAttribute('class', 'left-align');
                            $this->hasColorField = true;
                        }
                    } else {
                        $el = new Fieldset();

                        $title = new Element\Text('title');
                        $title->setAttributes(array(
                            'size' => $configData['text_field']['text_field_size'],
                            'maxLength' => $configData['text_field']['text_field_max_length'],
                            'placeholder' => $configData['text_field']['text_field_watermark'] . t(':title'),
                        ))->setLabel('Title');

                        $url = new Element\Text('url');
                        $url->setAttributes(array(
                            'size' => $configData['text_field']['text_field_size'],
                            'maxLength' => $configData['text_field']['text_field_max_length'],
                            'placeholder' => $configData['text_field']['text_field_watermark'] . t(':url'),
                        ))->setLabel('Link');

                        $el->add($title);
                        $el->add($url);
                        $isFieldset = true;

                        $__inputFilterConfig = array();
                        $__inputFilterConfig['url'] = array(
                            'name' => 'url',
                            'filters' => array(
                                array('name' => 'Zend\Filter\UriNormalize'),
                                array('name' => 'Zend\Filter\StringTrim'),
                                array('name' => 'Zend\Filter\StripTags'),
                            )
                        );
                        $__inputFilterConfig['title'] = array(
                            'name' => 'title',
                            'filters' => array(
                                array('name' => 'Zend\Filter\StringTrim'),
                                array('name' => 'Zend\Filter\HtmlEntities'),
                            )
                        );

                        if (!is_array($inputFilterConfig))
                            $this->inputFilterConfig[$name] = $__inputFilterConfig;
                        else
                            $inputFilterConfig[$name] = $__inputFilterConfig;
                    }
                    break;
                case 'long_text':
                    $el = new Element\Textarea();
                    $el->setAttributes(array(
                        'maxLength' => $configData['long_text_field']['long_text_maxLength'],
                        'cols' => $configData['long_text_field']['long_text_cols'],
                        'rows' => $configData['long_text_field']['long_text_rows'],
                    ));
                    break;
                case 'checkBox':
                    $keyValuePairs = $configData['checkBox_field']['keyValuePairs'];
                    if (count($keyValuePairs) === 1) {
                        $el = new Element\Checkbox();
                        $item = array_shift($keyValuePairs);
                        $el->setValue($item['field_key']);
                    } else {
                        $el = new Element\MultiCheckbox();
                        $value_options = array();
                        foreach ($keyValuePairs as $items) {
                            $value_options[$items['field_key']] = $items['field_value'];
                        }
                        $el->setValueOptions($value_options);
                    }
                    break;
                case 'radio':
                    $el = new Element\Radio();
                    $keyValuePairs = $configData['radio_field']['keyValuePairs'];
                    $value_options = array();
                    foreach ($keyValuePairs as $items) {
                        $value_options[$items['field_key']] = $items['field_value'];
                    }
                    $el->setValueOptions($value_options);
                    break;
                case 'select':
                    $el = new Element\Select();
                    $keyValuePairs = $configData['select_field']['keyValuePairs'];
                    $sortStatus = (isset($configData['select_field']['sort']))? (int)$configData['select_field']['sort'] : 0;
                   // $value_options = array();

                    //change select for cache
                    $cache_key = 'select_' . $name;
                    if (!$value_options = getCache()->getItem($cache_key)) {
                        foreach ($keyValuePairs as $items) {
                            $value_options[$items['field_key']] = $items['field_value'];
                        }
                        if($sortStatus) //TODO CHECK SORT DIRECTION
                        {
                            $firstValue = array_slice($value_options,0,1);
                            array_shift($value_options);
                            $value_options = psort($value_options);
                            $value_options = $firstValue + $value_options;
                            getCache()->setItem($cache_key, $value_options);
                        }
                    }
                    $el->setValueOptions($value_options);
                    $size = $configData['select_field']['select_field_size'];
                    $el->setAttribute('class', 'select2');
                    if ($configData['select_field']['select_field_select_count'] > 1) {
                        $el->setAttribute('multiple', 'multiple');
                        if ($size <= 1)
                            $size = count($value_options);
                    }

                    $el->setAttributes(array(
                        'size' => $size,
                    ));
                    break;
                case 'uniqueCode':
                    $el = new UniqueCode();
                    break;
                case 'barcode':
                    $el = new Barcode();
                    break;
                case 'currentDate':
                    $el = new CurrentDate();
                    break;
                case 'fileUpload':
                    $this->hasFileUploadField = true;
                    $this->fileUploadFields[] = $field;
                    $isFieldset = true;
                    $el = new Fieldset();
                    $el->add(new Element\File('path'));

                    if ($forEdit) {
                        $preview = new StaticElement('preview');
                        $preview->setLabel('Uploaded File');
                        $el->add($preview);
                    }

                    $__inputFilterConfig = array();
                    if (isset($configData['fileUpload_field']['alt']) && $configData['fileUpload_field']['alt'] == '1') {
                        $alt = new Element\Text('alt');
                        $alt->setAttribute('placeholder', t('IMAGE_ALT'));
                        $alt->setLabel('IMAGE_ALT');
                        $el->add($alt);

                        $__inputFilterConfig['alt'] = array(
                            'name' => 'alt',
                            'filters' => array(
                                array('name' => 'Zend\Filter\StringTrim'),
                                array('name' => 'Zend\Filter\StripTags'),
                            )
                        );
                    }
                    if (isset($configData['fileUpload_field']['title']) && $configData['fileUpload_field']['title'] == '1') {
                        $title = new Element\Text('title');
                        $title->setAttribute('placeholder', t('IMAGE_TITLE'));
                        $title->setLabel('IMAGE_TITLE');
                        $el->add($title);
                        $__inputFilterConfig['title'] = array(
                            'name' => 'title',
                            'filters' => array(
                                array('name' => 'Zend\Filter\StringTrim'),
                                array('name' => 'Zend\Filter\HtmlEntities'),
                            )
                        );
                    }
                    if (isset($configData['fileUpload_field']['link']) && $configData['fileUpload_field']['link'] == '1') {
                        $link = new Element\Text('link');
                        $link->setAttribute('placeholder', t('IMAGE_LINK'));
                        $link->setLabel('IMAGE_LINK');
                        $el->add($link);
                        $__inputFilterConfig['link'] = array(
                            'name' => 'link',
                            'filters' => array(
                                array('name' => 'Zend\Filter\UriNormalize'),
                                array('name' => 'Zend\Filter\StringTrim'),
                                array('name' => 'Zend\Filter\StripTags'),
                            )
                        );
                    }

                    if (!array_key_exists('Zend\Validator\File\ExcludeExtension', $field->validators)) {
                        $__inputFilterConfig['path']['filters'][] = array(
                            'name' => 'Zend\Validator\File\ExcludeExtension',
                            'options' => array('extension' => array('php', 'js', 'html', 'htm', 'exe', 'pl', 'sh'))
                        );
                    } else {
                        foreach ($__inputFilterConfig['path']['filters'] as $filter) {
                            if ($filter['nmae'] == 'Zend\Validator\File\ExcludeExtension') {
                            }
                        }
                    }
                    if (!isset($field->validators['Zend\Validator\File\ExcludeExtension'])) {
                        $field->validators['Zend\Validator\File\ExcludeExtension'] = array(
                            'apply' => '1',
                            'Extension' => array()
                        );
                    }

                    $field->validators['Zend\Validator\File\ExcludeExtension']['Extension'] =
                        array_merge($field->validators['Zend\Validator\File\ExcludeExtension']['Extension'],
                            array(
                                array('field_key' => 'php'),
                                array('field_key' => 'js'),
                                array('field_key' => 'html'),
                                array('field_key' => 'htm'),
                                array('field_key' => 'exe'),
                                array('field_key' => 'pl'),
                                array('field_key' => 'sh')
                            ));
                    $inputFilters = $this->_makeInputFilters('path', $required, $allow_empty, $field->validators, $field->filters);
                    $__inputFilterConfig['path'] = $inputFilters;

                    if (!is_array($inputFilterConfig))
                        $this->inputFilterConfig[$name] = $__inputFilterConfig;
                    else
                        $inputFilterConfig[$name] = $__inputFilterConfig;

                    $field->filters = array();
                    $field->validators = array();
                    break;
                case 'collection':
//                    $this->collectionFields[] = $field;
                    $__config = $configData['collection_field'];
                    $__fields = $__config['fields'];
                    $targetElement = null;
                    if (count($__fields)) {

                        $this->hasCollectionField = true;

                        $itemCount = isset($__config['itemCount']) ? (int)$__config['itemCount'] : 1;
                        $allowAdd = isset($__config['allowAdd']) ? (int)$__config['allowAdd'] : false;
                        $allowRemove = isset($__config['allowRemove']) ? (int)$__config['allowRemove'] : false;
                        $targetElementClass = isset($__config['targetElementClass']) ? $__config['targetElementClass'] : '';
                        $targetElementColumnClass = isset($__config['targetElementColumnClass']) ? $__config['targetElementColumnClass'] : false;

                        $targetElement = new TargetElement();
                        $class = $targetElement->getAttribute('class');
                        $targetElement->setAttribute('class', $class . ' ' . $targetElementClass);
                        if ($targetElementColumnClass)
                            $targetElement->setOption('column_class', $targetElementColumnClass);

                        $__inputFilterConfig = array();
                        $this->loadFieldsById($__fields, null, $targetElement, $__inputFilterConfig);
                        $targetElement->setInputFilterSpecification($__inputFilterConfig);
                        if ($allowRemove) {
                            $targetElement->add(array(
                                'name' => 'drop_collection_item',
                                'options' => array(
                                    'label' => '',
                                    'description' => '',
                                    'glyphicon' => 'remove text-danger'
                                ),
                                'attributes' => array(
                                    'type' => 'button',
                                    'value' => t('Delete This Item'),
                                    'title' => t('Delete This Item'),
                                    'class' => 'btn btn-default drop_collection_item',
                                ),
                            ));
                        }

                        $options = array(
//                            'label' => 'Collection',
                            'count' => (int)$itemCount,
                            'allow_add' => $allowAdd == '1' ? true : false,
                            'allow_remove' => $allowRemove == '1' ? true : false,
                            'should_create_template' => $allowAdd == '1' ? true : false,
                            'target_element' => $targetElement
                        );

                        $collection = $this->getFormFactory()->create(array(
                            'type' => 'Zend\Form\Element\Collection',
                            'name' => 'items',
                            'options' => $options,
                            'attributes' => array(
                                'class' => 'collection-container'
                            ),
                        ));

//                        $collection = new Element\Collection('items');
//                        $collection->setLabel('Collection');
//                        $collection->setAttributes(array(
//                            'class' => 'collection-container'
//                        ));
                        $collection->setLabelAttributes(array('class' => 'hidden'));
//                        $collection->setOptions($options);

                        $el = new Fieldset();

                        $note = isset($__config['note']) ? trim($__config['note']) : '';
                        $el->setOptions(array(
                            'description' => $note
                        ));
                        $el->add($collection);

                        if ($allowAdd) {
                            $el->add(array(
                                'name' => 'add_more_select_option',
                                'options' => array(
                                    'label' => '',
                                    'description' => '',
                                    'glyphicon' => 'plus text-success',
                                ),
                                'attributes' => array(
                                    'type' => 'button',
                                    'title' => t('Add More Select Options'),
                                    'value' => t('Add More'),
                                    'class' => 'btn btn-default add_collection_item',
                                ),
                            ));
                        }

                        $targetElementLayout = isset($__config['targetElementLayout']) ? $__config['targetElementLayout'] : false;
                        if ($targetElementLayout && $targetElementLayout == 'inline-collection') {
                            $class = $targetElement->getAttribute('class');
                            $targetElement->setAttribute('class', $class . ' inline-collection');
                        } elseif ($targetElementLayout && $targetElementLayout != 'none') {
                            $targetElement->setOption('twb-layout', $targetElementLayout);

                            if ($targetElementLayout == 'horizontal') {
                                if (isset($__config['horizontal_layout'])) {
                                    $labelClass = isset($__config['horizontal_layout']['label_class']) ? $__config['horizontal_layout']['label_class'] : 'col-md-2';
                                    $column_size = isset($__config['horizontal_layout']['column_size']) ? $__config['horizontal_layout']['column_size'] : 'md-10';
                                    /* @var $tEl Element */
                                    foreach ($targetElement->getElements() as $tEl) {
                                        $tEl->setOptions(array(
                                            'column-size' => $column_size,
                                            'label_attributes' => array('class' => $labelClass)
                                        ));
                                    }
                                }
                            }
                        }
                    }

                    if (!is_array($inputFilterConfig))
                        $this->inputFilterConfig[$name] = array();
                    else
                        $inputFilterConfig[$name] = array();

                    break;
            }

            if ($el) {
                if (!empty($postFix))
                    $el->setOptions(array('postFix' => $postFix));
                if (!empty($label))
                    $el->setLabel($label);
                $el->setName($name);
                if (!in_array($field->getFieldType(), array('uniqueCode', 'barcode', 'currentDate')))
                    $el->setValue($field->getFieldDefaultValue());
                $el->fieldId = $field->id;

                $fields_set->add($el);
            }
        }

        if ($inputFilterConfig)
            return $inputFilterConfig;

        return $this->inputFilterConfig;
    }

    private function _makeInputFilters($name, $required, $allow_empty, $validators, $filters)
    {
        $inputFilter = array(
            'name' => $name,
            'required' => $required,
            'allow_empty' => $allow_empty,
        );
        if (is_array($validators) && count($validators)) {
            foreach ($validators as $name => $params) {
                if (is_array($params)) {
                    if (isset($params['apply']) && $params['apply'] == '1') {
                        unset($params['apply']);

                        switch ($name) {
                            case 'Zend\Validator\File\ExcludeExtension':
                            case 'Zend\Validator\File\ExcludeMimeType':
                            case 'Zend\Validator\File\Extension':
                            case 'Zend\Validator\File\MimeType':
                                if (isset($params['Extension'])) {
                                    $Extension = $params['Extension'];
                                    unset($params['Extension']);
                                    $exts = array();
                                    foreach ($Extension as $items) {
                                        $exts[] = $items['field_key'];
                                    }
                                    $params['extension'] = $exts;
                                } elseif (isset($params['MimeType'])) {
                                    $Extension = $params['MimeType'];
                                    unset($params['MimeType']);
                                    $exts = array();
                                    foreach ($Extension as $items) {
                                        $exts[] = $items['field_key'];
                                    }
                                    $params['mimeType'] = $exts;
                                }
                                break;
                        }

                        $options = array();
                        if (count($params)) {
                            $options = $params;
                        }

                        $inputFilter['validators'][] = array(
                            'name' => $name,
                            'options' => $options
                        );
                    }
                }
            }
        }
        if (is_array($filters) && count($filters)) {
            foreach ($filters as $name => $params) {
                if (is_array($params)) {
                    if (isset($params['apply']) && $params['apply'] == '1') {
                        unset($params['apply']);
                        $options = array();
                        if (count($params)) {
                            $options = $params;
                        }
                        $inputFilter['filters'][] = array(
                            'name' => $name,
                            'options' => $options
                        );
                    }
                }
            }
        }
        return $inputFilter;
    }

    public function loadFieldsByType($entityType, Form $form, ElementInterface $placeHolder = null, $forEdit = false)
    {
        $data_list = $this->getFieldsTable()->getByEntityType($entityType);
        if ($placeHolder) {
            $fields_set = $placeHolder;
        } else
            $fields_set = $form;

        $null = null;
        $inputFilterConfig = $this->loadFields($data_list, $fields_set, $null, $forEdit);
        return $inputFilterConfig;
    }

    /**
     * @param array $ids
     * @param Form|null $form
     * @param ElementInterface $placeHolder if provided elements should be added to this element other wise to the form itself
     * @return array
     */
    public function loadFieldsById(array $ids, $form, ElementInterface $placeHolder = null, &$inputFilterConfig = null)
    {
        if (!count($ids))
            return null;

        $data_list = $this->getFieldsTable()->getById($ids);
        if ($placeHolder) {
            $fields_set = $placeHolder;
        } else
            $fields_set = $form;

        return $this->loadFields($data_list, $fields_set, $inputFilterConfig);
    }

    public function getFieldData($entityId, $fieldIds = array(), $forView = false, $editable = true)
    {
        if ($fieldIds && is_array($fieldIds) && count($fieldIds))
            $fields = getSM()->get('fields_table')->getById($fieldIds)->toArray();
        else
            $fields = $this->getFieldsTable()->getByEntityType($this->entityType)->toArray();

        $table_gateway = new BaseTableGateway($this->table_name);
        $data = array_shift($table_gateway->select(array('entityId' => $entityId))->toArray());

        foreach ($fields as $field) {
            $mName = $field['fieldMachineName'];
            if (isset($data[$mName])) {
                $fieldConfigData = unserialize($field['fieldConfigData']);
                $fieldType = $field['fieldType'];

                switch ($fieldType) {
                    case 'text':
                        if ($fieldConfigData['text_field']['text_type'] == 'text_web')
                            $data[$mName] = unserialize($data[$mName]);
                        break;
                    case 'long_text':
                        break;
                    case 'radio':
                        if (!has_value($data[$mName]))
                            $data[$mName] = '';
                        break;
                    case 'checkBox':
//                        if (!has_value($data[$mName]))
//                            $data[$mName] = '';
//                        else {
//                            if (!is_array($data[$mName]))
//                                $data[$mName] = explode(',', $data[$mName]);
//                        }
//                        break;
                    case 'select':
                        if (has_value($data[$mName])) {
                            $keyValuePairs = $fieldConfigData[$field['fieldType'] . '_field']['keyValuePairs'];

                            //checkbox and select
                            if (count($keyValuePairs) > 1 && $fieldType != 'radio') {
                                //select
                                if ($fieldType == 'select' && (isset($fieldConfigData['select_field']['select_field_select_count']) && (int)$fieldConfigData['select_field']['select_field_select_count'] == 1)) {
                                    if ($forView) {
                                        foreach ($keyValuePairs as $item) {
                                            if ($item['field_key'] == $data[$mName]) {
                                                $data[$mName] = $item['field_value'];
                                                break;
                                            }
                                        }
                                    }
                                } else {
                                    $data[$mName] = unserialize($data[$mName]);
                                }
                            } else {
                                $field_value = current($keyValuePairs);
                                if ($field_value['field_key'] !== $data[$mName])
                                    $data[$mName] = '';
                            }
                        }
                        break;
                    case 'currentDate':
                        if (!$editable)
                            unset($data[$mName]);
                        break;
                    case 'uniqueCode':
                        if (!$editable)
                            unset($data[$mName]);
                        break;
                    case 'barcode':
                        if (!$editable)
                            unset($data[$mName]);

                        if (isset($data[$mName]) && $forView) {
                            $img = "<img src='%s' alt='%s'/>";

                            $data[$mName] = sprintf($img, App::siteUrl() . url('barcode', array('barcode' => $data[$mName])), $data[$mName]);
                        }
                        break;
                    case 'fileUpload':
                        $data[$mName] = unserialize($data[$mName]);
                        $data[$mName]['preview'] = Common::Link(
                            t('Uploaded File') . ' : ' . end(explode('/', $data[$mName]['path'])),
                            $data[$mName]['path'],
                            array('target' => '_blank')
                        );
                        break;
                    case 'collection':
                        $data[$mName] = unserialize($data[$mName]);
                        break;
                    default:
                }
            }
        }
        return $data;
    }

    public function save($entityType, $entityId, $fields, $field_ids = null)
    {
        $fields['entityId'] = $entityId;
        $this->init($entityType);
        $columns_list = array_keys($fields);
        $this->__compareColumns($columns_list, $field_ids);

        if ($this->hasFileUploadField) {
            /* @var $field Field */
            foreach ($this->fileUploadFields as $field) {
                $mName = $field->getFieldMachineName();
                if (!isset($fields[$mName]))
                    $fields[$mName] = null;
                elseif (!isset($fields[$mName]['path']))
                    $fields[$mName] = null;
                elseif (!isset($fields[$mName]['path']['name']) || empty($fields[$mName]['path']['name']))
                    $fields[$mName] = null;
                else {
                    $path = File::MoveUploadedFile($fields[$mName]['path']['tmp_name'], PUBLIC_FILE . '/formmanager', $fields[$mName]['path']['name']);
                    $fields[$mName]['path'] = $path;
                }

                if ($fields[$mName] == null)
                    unset($fields[$mName]);
            }
        }

        foreach ($fields as $key => $f) {
            if (is_array($f))
                $fields[$key] = serialize($f);
        }

        $table_gateway = new BaseTableGateway($this->table_name);

        if (!isset($fields['id'])) {
            $table_gateway->insert($fields);
        } else {
            unset($fields['id']);
            $table_gateway->update($fields, array('entityId' => $entityId));
        }

    }

    public function remove($entityId)
    {
        $table_gateway = new BaseTableGateway($this->table_name);
        $table_gateway->delete(array('entityId' => $entityId));
    }

    private function __getFields($ids)
    {
        $fields_list = array();
        if ($ids && is_array($ids) && count($ids))
            $data_list = $this->getFieldsTable()->getById($ids);
        else
            $data_list = $this->getFieldsTable()->getByEntityType($this->entityType);
        /* @var $field \Fields\Model\Field */
        foreach ($data_list as $field) {
            $field->setFieldConfigData(unserialize($field->getFieldConfigData()));
            $fields_list[$field->getFieldMachineName()] = $field;
        }
        return $fields_list;
    }

    public function getFields(array $ids)
    {
        $data_list = $this->getServiceLocator()->get('fields_table')->getById($ids)->toArray();
        for ($i = 0; $i < count($data_list); $i++) {
            $data_list[$i]['fieldConfigData'] = unserialize($data_list[$i]['fieldConfigData']);
        }
        return $data_list;
    }

    private function __compareColumns($columns_list, $ids)
    {
        //TODO remove old columns
        $fields = $this->__getFields($ids);
        foreach ($columns_list as $col) {
            if (!in_array($col, $this->cache_item['columns'])) {
                $table_name = $this->adapter->getPlatform()->quoteIdentifier($this->table_name);
                $col_name = $this->adapter->getPlatform()->quoteIdentifier($col);
                if (isset($fields[$col])) {
                    /* @var $field \Fields\Model\Field */
                    $field = $fields[$col];
                    $type = $this->getSqlType($field);
                } else
                    $type = 'varchar(255)';
//                $type = 'text';
                $q = "ALTER TABLE {$table_name} ADD COLUMN {$col_name} {$type} DEFAULT NULL";
                $statement = $this->adapter->query($q);
                $statement->execute();
                $this->cache_item['columns'][] = $col;
            }
        }
        setCacheItem($this->table_cache_key, $this->cache_item);
    }

    private function __checkColumns()
    {
        if (!isset($this->cache_item['columns'])) {
            $table_name = $this->adapter->getPlatform()->quoteIdentifier($this->table_name);
            $q = "SHOW COLUMNS FROM {$table_name}";
            $result = $this->adapter->query($q, Adapter::QUERY_MODE_EXECUTE);
            $columns_list = array();
            foreach ($result as $column) {
                $columns_list[] = $column->Field;
            }
            $this->cache_item['columns'] = $columns_list;
            setCacheItem($this->table_cache_key, $this->cache_item);
        }
    }

    private function __checkTable()
    {
        if (!cacheExist($this->table_cache_key)) {
            $table_name = $this->adapter->getPlatform()->quoteIdentifier($this->table_name);
            $q = "CREATE TABLE IF NOT EXISTS {$table_name} (
                    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
                    `entityId` int(11) NOT NULL,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB
                  CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";


            $statement = $this->adapter->query($q);
            $statement->execute();
            $this->cache_item['table'] = $this->table_name;
            setCacheItem($this->table_cache_key, $this->cache_item);
        }
    }

    private function makeTableName($entityType)
    {
        return 'tbl_filed_data_' . $entityType;
    }

    public function getCacheKey($entityType)
    {
        return 'table_name_' . $this->makeTableName($entityType);
    }

    public function getArrayFields($fields, $type = 1) //type = 1 : key->field Id & val->name , type = 2 : key->machine name & val->name , type = 3 : key -> machine name & val->id
    {
        $dataArray = array();
        if (is_array($fields)) {
            foreach ($fields as $row) {
                if ($type == 1)
                    $dataArray[$row['id']] = $row['fieldName'];
                elseif ($type == 2)
                    $dataArray[$row['fieldMachineName']] = $row['fieldName'];
                elseif ($type == 3)
                    $dataArray[$row['fieldMachineName']] = $row['id'];
            }
        }
        return $dataArray;
    }

    /**
     * @return \Fields\Model\FieldTable
     */
    private function getFieldsTable()
    {
        return getSM('fields_table');
    }

    private function getFormFactory()
    {
        if ($this->formFactory == null)
            $this->formFactory = new Factory();
        return $this->formFactory;
    }

    public function RenderCollection($data, $field = null, $view = null)
    {
        $title = '';
        $targetElementClass = '';
        $targetElementColumnClass = '';
        $machineName = '';
        $config = null;
        $showTitle = true;
        $showNote = true;
        if ($field) {
            $config = $field['fieldConfigData']['collection_field'];
            $targetElementClass = isset($config['view']['class']) ? trim($config['view']['class']) : '';
            $targetElementColumnClass = isset($config['view']['columnClass']) ? trim($config['view']['columnClass']) : '';

            if (isset($config['view'])) {
                if (!$view)
                    $view = $config['view']['type'];

                $showTitle = $config['view']['title'] == '1';
                $showNote = $config['view']['note'] == '1';
            }
            $title = $field['fieldName'];
            $machineName = $field['fieldMachineName'];


        }
        if (!$view)
            $view = 'none';

        if ($view && $view != 'none') {
            $output = '';

            if (isset($config['fields']) && count($config['fields'])) {
                $fields = $this->getFields($config['fields']);
                $items = array();
                if (isset($data['items'])) {
                    foreach ($data['items'] as $index => $item) {
                        $items[] = $this->generate($fields, $item);
                    }
                }
                if (count($items)) {
                    switch ($view) {
                        case 'comma':
                        case 'space':

                            $__items = array();
                            foreach ($items as $index => $item) {
                                foreach ($item as $key => $value) {
                                    $__items[] = $value;
                                }
                            }

                            $separator = ' ';
                            if ($view == 'comma')
                                $separator = ',';

                            $output = implode($separator, $__items);
                            break;
                        case 'table':
                        case 'simple-table':
                            $headerLoaded = false;
                            $rows = array();
                            $headers = array();
                            foreach ($items as $index => $item) {
                                $row = array();
                                foreach ($item as $key => $value) {
                                    $row[] = $value;
                                    if ($view == 'table' && !$headerLoaded)
                                        $headers[] = $key;
                                }
                                $headerLoaded = true;
                                $rows[] = $row;
                            }
                            $output = "<div class='table-responsive'>" . Table::Table($headers, $rows,
                                    array('class' =>
                                        array('table table-stripped table-hover table-condensed')
                                    )
                                ) . "</div>";
                            break;

                        case 'block-dl':
                        case 'block-dl-h':
                            $dlClass = $view == 'block-dl-h' ? 'dl-horizontal' : '';
                            foreach ($items as $index => $item) {
                                if (!empty($targetElementClass))
                                    $output .= "<div class='{$targetElementColumnClass}'>";

                                $output .= "<div class='field_collection_view_item field_collection_view_{$machineName}_item field_collection_view_{$machineName}_item_{$index} {$targetElementClass}'>
                                                <dl class='{$dlClass}'>";
                                foreach ($item as $key => $value) {
                                    $output .= "<dt>{$key}</dt><dd>{$value}</dd>";
                                }
                                $output .= "</dl></div>";

                                if (!empty($targetElementClass))
                                    $output .= "</div>";
                            }
                            break;
                    }
                }
            }

            if ($view != 'none' && $view != 'comma') {
                $note = isset($config['note']) ? strip_tags(trim($config['note'])) : '';
                if (!empty($note) && $showNote)
                    $note = "<p class='help-block'>{$note}</p>";

                $title = $showTitle ? "<h3>{$title}</h3>" : '';

                $output = "<div id='field_collection_{$machineName}'
                                class='field_collection_view_items field_collection_view_{$machineName}_items'>
                                {$title}
                                {$note}{$output}</div>";
            }

            return $output;
        }

        //no view config is found, defaults to none, return as array
        return $data;
    }

    public function getSqlType($fieldObject)
    {
        $type = 'varchar(255)';
        $field = (array)$fieldObject;
        if (!is_array($field['fieldConfigData']))
            $field['fieldConfigData'] = @unserialize($field['fieldConfigData']);

        switch ($field['fieldType']) {
            case 'text':
                $text_length = (int)$field['fieldConfigData']['text_field']['text_field_max_length'];
                switch ($field['fieldConfigData']['text_field']['text_type']) {
                    case 'integer':
                    case 'decimal':
                        if ($text_length < 3)
                            $type = "TINYINT(" . $text_length . ")";
                        elseif ($text_length >= 3 && $text_length < 5)
                            $type = "SMALLINT(" . $text_length . ")";
                        elseif ($text_length >= 5 && $text_length < 7)
                            $type = "MEDIUMINT(" . $text_length . ")";
                        elseif ($text_length >= 7 && $text_length < 10)
                            $type = "INT(" . $text_length . ")";
                        elseif ($text_length >= 10 && $text_length < 19)
                            $type = "BIGINT(" . $text_length . ")";
                        elseif ($text_length >= 19)
                            $type = "BIGINT(19)";
                        break;
                    default:
                        $type = "varchar(" . $field['fieldConfigData']['text_field']['text_field_max_length'] . ")";
                        break;
                }
                break;
            case 'long_text':
                $len = 0;
                if (isset($field['fieldConfigData']['long_text_field']['long_text_maxLength']))
                    $len = $field['fieldConfigData']['long_text_field']['long_text_maxLength'];
                if ($len <= 400)
                    $type = "varchar(" . $field['fieldConfigData']['long_text_field']['long_text_maxLength'] . ")";
                else
                    $type = "text";
                break;
            case 'checkBox':
                $keyValuePairs = $field['fieldConfigData']['checkBox_field']['keyValuePairs'];
                if (count($keyValuePairs) === 1) {
                    $type = "tinyint(1)";
                } else {
                    $type = "text";
                }
                break;
            case 'radio':
                $type = "varchar(200)";
                break;
            case 'select':
                $type = "text";
                break;
            case 'collection':
                $type = "text";
                break;
            default:
                $type = 'varchar(255)';
        }

        return $type;
    }
}