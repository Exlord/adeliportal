<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/24/13
 * Time: 10:08 AM
 */

namespace DataView\API;

class CustomColumn extends Column
{
    protected $data;

    /**
     * @param $name
     * @param string $header
     * @param callable $data
     */
    public function __construct($name, $header, $data)
    {
        $this->data = $data;
        parent::__construct($name, $header, '', false);
    }

    protected function init()
    {
        $this->addAttr('class', 'custom-column');
    }

    protected function getData($data)
    {
        $data_f = $this->data;
        return $data_f($data);
    }
}