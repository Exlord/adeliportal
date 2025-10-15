<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/24/13
 * Time: 10:08 AM
 */

namespace DataView\API;

class NestedColumn extends Column
{
    protected function init()
    {
        $this->addAttr('class', 'nested-column');
    }

    protected function getData($data){
        $prefix = ($data->indent ? '|' : '') . str_repeat('---', $data->indent);
        return $prefix . $data->{$this->getName()};
    }
}