<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/13/13
 * Time: 2:34 PM
 */

namespace DataView\Lib;


class Date extends Column
{
    public $dateType = 0;
    public $timeType = -1;
    public $sortable = true;

    public function __construct($field, $title, $options = array(), $dateType = 0, $timeType = -1)
    {
        parent::__construct($field, $title, $options);
        $this->hasTextFilter = false;
        $this->dateType = $dateType;
        $this->timeType = $timeType;
    }

    public function getValue()
    {
        $value = parent::getValue();
        if ($value) {
            /* @var $dateFormat callable */
            $dateFormat = $this->_dataGrid->_viewHelperManager->get('dateFormat');
            return $dateFormat($value, $this->dateType, $this->timeType);
        }
    }
}