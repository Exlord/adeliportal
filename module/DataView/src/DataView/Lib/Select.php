<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 10:03 AM
 */

namespace DataView\Lib;


class Select extends Visualizer
{
    public $route = false;

    /**
     * @param $field
     * @param $title
     * @param array $selectData
     * @param array $map An array of field values and css classes
     * @param array $options
     * @param bool $sortable
     */
    public function __construct($field, $title, $selectData, $map = array(), $options = array(), $sortable = true)
    {
        if (!isset($options['contentAttr']['align']))
            $options['attr']['align'] = 'center';
        $options['contentAttr']['class'][] = 'grid_select';

        parent::__construct($field, $title, $map, $selectData, $options, $sortable);
        $this->hasTextFilter = false;

        $script = "Grid['" . $this->getName() . "'] = " . json_encode($this->valueClass) . ';';
        $this->_headScript->appendScript($script);
    }

    public function getValue()
    {
        if ($this->isRowLocked())
            return $this->renderLockedColumn();
        $id = $this->_dataGrid->getIdCell()->getValue();
        $this->contentAttr['data-route'] = url($this->getRoute() , $this->_dataGrid->routeParams);
        $this->contentAttr['data-id'] = $id;
        $this->contentAttr['name'] = $this->getName();
        $this->contentAttr['id'] = $this->getName() . $id;

        $oValue = $this->dataRow->{$this->_filed};
        if (isset($this->valueClass[$oValue])) {
            $this->attr['class'][] = $this->valueClass[$oValue];
        }

        return sprintf($this->_selectTemplate,
            $this->renderAttr($this->contentAttr),
            $this->renderSelectOptions($this->visualValues, false));
    }

    public function render()
    {
        $value = $this->dataRow->{$this->_filed};
        //add class to attr if exist

        //render parent
        $result = parent::render();
        //remove the added class from attr
        if (isset($this->valueClass[$value])) {
            if (($key = array_search($this->valueClass[$value], $this->attr['class'])) !== false) {
                unset($this->attr['class'][$key]);
            }
        }
        return $result;
    }

    private function getRoute()
    {
        if (!$this->route)
            $this->route = $this->_dataGrid->route. '/update';
        return $this->route;
    }
}