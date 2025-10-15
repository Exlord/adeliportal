<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/13/13
 * Time: 2:41 PM
 */

namespace DataView\Lib;


class EditButton extends Button
{
    protected function _configRoute()
    {
        $this->routeParams['id'] = $this->_dataGrid->getIdCell()->getValue();
        if (!$this->route) {
            $this->route = $this->_dataGrid->route . '/edit';
            $this->routeParams = array_merge_recursive($this->routeParams, $this->_dataGrid->routeParams);
//            $this->routeOptions = array_merge_recursive($this->routeOptions, $this->_dataGrid->routeOptions);
        }
    }

    public function __construct()
    {
        $options['contentAttr']['class'][] = 'btn-default';
        $options['contentAttr']['icon'] = 'glyphicon glyphicon-edit';
        $options['contentAttr']['class'][] = 'ajax_page_load';
        parent::__construct('Edit',null, $options);
    }
}