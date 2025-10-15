<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/16/13
 * Time: 1:12 PM
 */

namespace DataView\Lib;


class RowSelector extends Checkbox
{
    public function __construct()
    {
        parent::__construct('rowId', '');
        $this->headerAttr['class'] = 'toggle_select';
        $this->contentAttr['class'][] = 'toggle_select_' . $this->getName() . '_item';
        $this->contentAttr['class'][] = 'row_selector';
        $this->sortable = false;
    }

    public function renderHeader()
    {
        $header = $this->_headerTemplate;
        $input = $this->_inputTemplate;
        $attr = array(
            'type' => $this->inputType,
            'id' => 'toggle_select_' . $this->getName(),
        );
        $input = sprintf($input, $this->renderAttr($attr));
        $header = sprintf($header, $this->renderAttr($this->headerAttr), $input);
        return $header;
    }
}