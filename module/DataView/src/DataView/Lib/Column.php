<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/12/13
 * Time: 1:39 PM
 */

namespace DataView\Lib;

use Zend\Form\Annotation\AbstractArrayOrStringAnnotation;

class Column extends SharedBase
{
    /**
     * @var DataGrid parent grid
     */
    protected $_dataGrid;
    /**
     * @var string db field name
     */
    protected $_filed;
    /**
     * @var string Name of the table for this field, defaults to the original table name if not provided
     */
    protected $_tableName;
    /**
     * @var string grid header title
     */
    protected $_title;

    protected $_headerTemplate = "<th %s>%s</th>";
    protected $_cellTemplate = "<td %s>%s</td>";
    protected $_selectTemplate = "<select %s>%s</select>";
    protected $lockedTemplate = "<span class='locked glyphicon glyphicon-lock text-muted grid-icon' title='%s'></span>";

    /**
     * Is this column sortable
     * @var bool
     */
    protected $sortable = true;


    public $dataRow = null;

    /**
     * Is this column visible (should it be rendered ?)
     * @var bool
     */
    public $visible = true;

    /**
     * Attributes for the cell
     * @var array
     */
    public $attr = array();

    /**
     * Attributes for the cell's content
     * @var array
     */
    public $contentAttr = array();

    /**
     * Attributes for the cell header
     * @var array
     */
    public $headerAttr = array();

    public $textFilterWatermark;
    public $hasTextFilter = true;
    public $hasSelectFilter = false;
    public $selectFilterData;
    public $isFirstColumn = false;
    public $isLastColumn = false;
    public $routeParams = array();
    public $routeOptions = array();

    public $lockedValue = null;
    public $lockedTitle = 'Locked';

    public $filterOperator = '=';
    public $filterValue = null;

    protected function renderSortHeader()
    {
        if ($this->sortable && $this->visible) {
            $sort_title_desc = t('Sort Descending');
            $sort_title_asc = t('Sort Ascending');
            $route_options = $this->_dataGrid->routeOptions;
            $route_options['query']['orderBy'] = $this->getName();
            $route_options['query']['orderDir'] = 'ASC';

            $route_params = $this->_dataGrid->routeParams;
            $url_sort = url($this->_dataGrid->route, $route_params, $route_options);
            $sort = "<a rel='tooltip' title='$sort_title_asc' href='$url_sort' class='sort-button sort-button-up ajax_page_load'><span class='ui-icon ui-icon-triangle-1-n'>▲</span></a>";

            $route_options['query']['orderDir'] = 'DESC';

            //TODO query string
            $url_sort = url($this->_dataGrid->route, $route_params, $route_options);
            $sort .= "<a rel='tooltip' title='$sort_title_desc' href='$url_sort' class='sort-button sort-button-down ajax_page_load'><span class='ui-icon ui-icon-triangle-1-s'>▼</span></a>";

            $sort = "<span class='grid-sort-buttons'>$sort</span>";
            return $sort;
        }
        return '';
    }

    protected function renderSelectOptions($data, $isFilter)
    {
        $template = '<option value="%s" %s>%s</option>';

        $options = '';
        if ($isFilter) {
            $empty = sprintf($template, '', '', t('-- Select --'));
            $selectedValue = $this->getFilterValue();
            $id = $this->getFilterId();
        } else {
            $selectedValue = self::getValue();
            $empty = '';
        }
        if ($data)
            foreach ($data as $key => $value) {
                $selected = $key == $selectedValue ? "selected='selected'" : '';
                if ($isFilter) {
                    $routeOptions = $this->_dataGrid->routeOptions;
                    $routeOptions['query'][$id] = $key;
                    $selectValue = url($this->_dataGrid->route, $this->_dataGrid->routeParams, $routeOptions);
                } else
                    $selectValue = $key;
                $options .= sprintf($template, $selectValue, $selected, $value);
            }

        return $empty . $options;
    }

    /**
     * @param $field
     * @param $title
     * @param array $options
     * @param bool $sortable
     */
    public function __construct($field, $title, $options = array(), $sortable = true)
    {
        parent::__construct();
        $this->_filed = $field;
        $this->_title = $title;
        $this->sortable = $sortable;
        if (isset($options['attr']))
            $this->attr = $options['attr'];
        if (isset($options['headerAttr']))
            $this->headerAttr = $options['headerAttr'];
        if (isset($options['contentAttr']))
            $this->contentAttr = $options['contentAttr'];

        $this->_headScript = $this->_viewHelperManager->get('headScript');
    }

    public function setDataGrid($dataGrid)
    {
        $this->_dataGrid = $dataGrid;
    }

    public function getName()
    {
        return $this->_filed;
    }

    public function renderHeader()
    {
        if ($this->visible) {
            $header = $this->_headerTemplate;
            $title = $this->getTitle();
            $value = $this->renderSortHeader() . $title;
            if ($value == '')
                $value = '&nbsp;';
            $header = sprintf($header, $this->renderAttr($this->headerAttr), $value);
            return $header;
        }
        return '';
    }

    public function renderSelectFilter()
    {
        if ($this->hasSelectFilter) {
            $label = sprintf("<label>%s</label>", $this->getTitle());
            $attr = array(
                'id' => $this->getFilterId()
            );
            $select = sprintf($this->_selectTemplate, $this->renderAttr($attr),
                $this->renderSelectOptions($this->selectFilterData, true));
            return $label . $select;
        }
        return '';
    }

    protected function renderLockedColumn()
    {
        return sprintf($this->lockedTemplate, t($this->lockedTitle));
    }

    public function render()
    {
        if ($this->visible) {
            if ($this->isFirstColumn)
                $this->attr['class'][] = 'first';
            if ($this->isLastColumn)
                $this->attr['class'][] = 'last';

            if ($this->lockedValue && $this->dataRow->{$this->_filed} == $this->lockedValue)
                $value = $this->renderLockedColumn();
            else
                $value = $this->getValue();
            if ($value == '')
                $value = '&nbsp;';

            return sprintf($this->_cellTemplate, $this->renderAttr($this->attr), $value);
        }
        return '';
    }

    public function getValue()
    {
        return $this->dataRow->{$this->_filed};
    }

    public function getTitle()
    {
        if (!empty($this->_title))
            return t($this->_title);
        return $this->_title;
    }

    /**
     * Generates an Id for the filters input/select element
     * @return string
     */
    public function getFilterId()
    {
        return 'grid_filter_' . $this->getName(); //input id
    }

    /**
     * Gets the submitted value of the filter if any
     * @return mixed
     */
    public function getFilterValue()
    {
        if (is_null($this->filterValue)) {
            $value = $this->_dataGrid->_paramsPlugin->fromQuery($this->getFilterId(), ''); //is any value has submitted
            return trim($value);
        } else
            return $this->filterValue;

    }

    public function renderFilter()
    {
        if ($this->visible) {
            $attr = array();
            if ($this->isFirstColumn)
                $attr['class'][] = 'first';
            if ($this->isLastColumn)
                $attr['class'][] = 'last';

            if ($this->hasTextFilter) {
                $template = "<input style='%s' title='%s' rel='tooltip' type='text' id='%s' value='%s' watermark='%s' data-url='%s'>";
                $id = $this->getFilterId(); //input id
                $watermark = $this->textFilterWatermark;
                if (empty($watermark)) {
                    $watermark = t('Search for ') . $this->getTitle(); //default watermark is "Search for <NAME>"
                }
                $value = $this->getFilterValue(); //is any value has submitted
                //url to submit including all other filters
                $url = url($this->_dataGrid->route, $this->_dataGrid->routeParams, $this->_dataGrid->routeOptions);
                //input field widths
                $width = '';
                if (isset($this->headerAttr['width']))
                    $width = 'width:' . $this->headerAttr['width'];
                //filter input element markup
                $filter = sprintf($template, $width, $watermark, $id, $value, $watermark, $url);
                $attr['class'][] = 'grid_filter_cell';

                //cell markup
                return sprintf($this->_cellTemplate, $this->renderAttr($attr), $filter);
            } else {
                $attr['class'][] = 'grid_no_filter_cell';
                return sprintf($this->_cellTemplate, $this->renderAttr($attr), '');
            }
        }
        return '';
    }

    public function setTableName($name)
    {
        $this->_tableName = $name;
        return $this;
    }

    public function getTableName()
    {
        if (has_value($this->_tableName))
            return $this->_tableName;
        else
            return $this->_dataGrid->getTableGateway()->table;
    }

    public function isRowLocked()
    {
        return (isset($this->dataRow->locked) && $this->dataRow->locked == 1);
    }
}