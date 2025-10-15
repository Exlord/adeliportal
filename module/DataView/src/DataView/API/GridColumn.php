<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/24/13
 * Time: 10:08 AM
 */

namespace DataView\API;


class GridColumn
{
    const TYPE_TEXT = 'grid_column_type_text';
    const TYPE_ORDER = 'grid_column_type_order';
    const TYPE_STATUS = 'grid_column_type_status';
    const TYPE_BUTTON = 'grid_column_type_button';
    const TYPE_CLOSURE = 'grid_column_type_closure';
    const TYPE_SELECT = 'grid_column_type_select';

    public $Name;
    public $Header;
    public $Type = self::TYPE_TEXT;
    public $DataUrl;
    public $Closure;

    //TODO filter columns
    //TODO filter types (= or like)

    /**
     * @var array
     */
    public $Class = array();

    /**
     * @var bool
     */
    public $Sortable = false;

    /**
     * @var int
     * 0=auto
     */
    private $Width = 0;

    public function __construct($name)
    {
        $this->Name = $name;
    }

    /**
     * @return $this
     */
    public function isSortable()
    {
        $this->Sortable = true;
        return $this;
    }

    public function setWidth($width)
    {
        $this->Width = $width;
        return $this;
    }

    public function getWidth()
    {
        if ($this->Width) {
            if (empty($this->Header)) {
                if ($this->Width < 18 && $this->Sortable)
                    $this->Width = 18;
            } else {
                if ($this->Width < 30 && $this->Sortable)
                    $this->Width = 30;
            }

            return $this->Width . 'px';
        } else
            return '';
    }

    /**
     * @param array $Class
     * @return GridColumn
     */
    public function setClass($Class)
    {
        $this->Class = $Class;
        return $this;
    }

    /**
     * @param $Class
     * @return GridColumn
     */
    public function addClass($Class)
    {
        if (!in_array($Class, $this->Class))
            $this->Class[] = $Class;
        return $this;
    }

    /**
     * @param $closures
     * @return GridColumn
     */
    public function setClosure($closures)
    {
        $this->Closure = $closures;
        return $this;
    }

    /**
     * @param $url
     * @return GridColumn
     */
    public function setDataUrl($url)
    {
        $this->DataUrl = $url;
        return $this;
    }
}