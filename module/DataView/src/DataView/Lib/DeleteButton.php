<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/13/13
 * Time: 2:41 PM
 */

namespace DataView\Lib;


class DeleteButton extends Button
{
    protected function _configRoute()
    {
        $this->contentAttr['data-id'] = $this->_dataGrid->getIdCell()->getValue();
        if (!$this->route) {
            $this->route = $this->_dataGrid->route . '/delete';
            $this->routeParams = array_merge_recursive($this->routeParams, $this->_dataGrid->routeParams);
//            $this->routeOptions = array_merge_recursive($this->routeOptions, $this->_dataGrid->routeOptions);
        }
    }

    public function __construct()
    {
        $options['contentAttr']['class'][] = 'btn-default delete_button';
        $options['contentAttr']['icon'] = 'glyphicon glyphicon-remove text-danger';
        parent::__construct('Delete', null, $options);
    }
}