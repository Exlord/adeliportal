<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/19/13
 * Time: 3:10 PM
 */

namespace DataView\Lib;


class Custom extends Column
{
    private $data;

    /**
     * @param $field
     * @param $title
     * @param callable|string $data
     * @param array $options
     * @param bool $sortable
     */
    public function __construct($field, $title, $data, $options = array(), $sortable = false)
    {
        parent::__construct($field, $title, $options, $sortable);
        $this->data = $data;
        $this->hasTextFilter = false;
    }

    public function getValue()
    {
        if (is_closure($this->data)) {
            $c = $this->data;
            return $c($this);
        }
        return $this->data;
    }
}