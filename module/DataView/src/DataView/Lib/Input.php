<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/13/13
 * Time: 10:36 AM
 */

namespace DataView\Lib;


use DataView\Lib\Column;

class Input extends Column
{
    protected $inputType = 'text';
    protected $_inputTemplate = "<input  %s>";

    /**
     * @param string $field The name of the column in database
     * @param string $title
     * @param array $options
     */
    public function __construct($field, $title, $options = array())
    {
        parent::__construct($field, $title, $options);
    }

    public function getValue()
    {
        $value = $this->_dataGrid->getIdCell()->getValue();

        $this->contentAttr['type'] = $this->inputType;
        $this->contentAttr['name'] = $this->getName();
        $this->contentAttr['id'] = $this->getName() . $value;
        $this->contentAttr['data-' . $this->_dataGrid->getIdCell()->getName()] = $value;
        if (isset($this->dataRow->{$this->_filed}))
            $attr['value'] = $this->dataRow->{$this->_filed};
        $value = sprintf($this->_inputTemplate, $this->renderAttr($this->contentAttr));
        return $value;
    }
}