<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/19/13
 * Time: 3:23 PM
 */

namespace DataView\Lib;


class Visualizer extends column
{
    protected $valueClass;
    protected $visualValues;

    /**
     * @param $field
     * @param $title
     * @param array $valueClass An array of field values and css classes
     * @param array $visualValues An array of field values and replace values
     * @param array $options
     * @param bool $sortable
     */
    public function __construct($field, $title, $valueClass, $visualValues = array(), $options = array(), $sortable = false)
    {
        parent::__construct($field, $title, $options, $sortable);
        $this->valueClass = $valueClass;
        $this->visualValues = $visualValues;
        $this->hasTextFilter = false;
    }

    public function getValue()
    {
        if ($this->isRowLocked())
            return $this->renderLockedColumn();
        $oValue = $value = parent::getValue();
        if (isset($this->visualValues[$oValue])) {
            $value = $this->visualValues[$oValue];
            $this->attr['title'] = $value;
        } else
            $this->attr['title'] = $oValue;

        if (isset($this->valueClass[$oValue]))
            $value = sprintf("<span class='%s'></span>", $this->valueClass[$oValue]);

        return $value;
    }

//    public function render()
//    {
//        $value = parent::getValue();
//        //add class to attr if exist
//
//        //render parent
//        $result = parent::render();
//        //remove the added class from attr
//        if (isset($this->valueClass[$value])) {
//            if (($key = array_search($this->valueClass[$value], $this->attr['class'])) !== false) {
//                unset($this->attr['class'][$key]);
//            }
//        }
//        return $result;
//    }
}