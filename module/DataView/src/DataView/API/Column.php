<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/24/13
 * Time: 10:08 AM
 */

namespace DataView\API;

use Zend\I18n\Translator\TranslatorAwareInterface;

class Column
{
    protected $Name;
    protected $Header;
    protected $attributes = array();

    /**
     * @var bool
     */
    protected $Sortable = false;

    /**
     * @var int
     * 0=auto
     */
    protected $Width = 0;

    protected $template = "<td %s>%s</td>";
    protected $header_template = "<th %s>%s</th>";

    /**
     * @var Grid
     */
    protected $parent;

    public function __construct($name, $header = '', $width = '', $sortable = true)
    {
        $this->Name = $name;
        $this->Header = $header;
        $this->Width = $width;
        $this->Sortable = false;
        $this->init();
    }

    protected function init()
    {
        $this->addAttr('class', 'text-column');
    }

    /**
     * @return GridColumn
     */
    public function isSortable()
    {
        $this->Sortable = true;
        return $this;
    }

    /**
     * @param string $Header
     * @return GridColumn
     */
    public function setHeader($Header)
    {
        $this->Header = $Header;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return sprintf($this->header_template, $this->getWidth(), $this->getSortHeader() . t($this->Header));
    }

    /**
     * @param mixed $Name
     * @return GridColumn
     */
    public function setName($Name)
    {
        $this->Name = $Name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * @param boolean $Sortable
     * @return GridColumn
     */
    public function setSortable($Sortable)
    {
        $this->Sortable = $Sortable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getSortable()
    {
        return $this->Sortable;
    }

    /**
     * @param int $Width
     * @return GridColumn
     */
    public function setWidth($Width)
    {
        $this->Width = $Width;
        return $this;
    }

    /**
     * @return int
     */
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

            return " width='" . $this->Width . "px'";
        } else
            return '';
    }

    protected function getData($data)
    {
        if (isset($data->{$this->getName()}))
            return $data->{$this->getName()};
    }

    protected function getContent($data)
    {
        return $this->getData($data);
    }

    public function render($data)
    {
        $attr = $this->makeAttrString();
        $dataHtml = $this->getContent($data);
        if ($dataHtml == null)
            $dataHtml = '';

        return sprintf($this->template, $attr, $dataHtml);
    }

    /**
     * @param \DataView\API\Grid $parent
     */
    public function setParent(Grid $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return \DataView\API\Grid
     */
    public function getParent()
    {
        return $this->parent;
    }

    protected function getSortHeader()
    {
        if ($this->Sortable) {

            $sort_title_desc = t('Sort Descending');
            $sort_title_asc = t('Sort Ascending');
            $sort_params = $this->parent->getRouteOptions();
            $sort_params['query']['order'] = 'asc_' . $this->getName();


            $route_params = $this->parent->getRouteParams();
            //TODO query string
            $url_sort = url($this->parent->getRoute(), $route_params, $sort_params);
            $sort = "<a title='$sort_title_asc' href='$url_sort' class='sort-button sort-button-up'><span class='ui-icon ui-icon-triangle-1-n'>▲</span></a>";

            $sort_params['query']['order'] = 'desc_' . $this->getName();
            //TODO query string
            $url_sort = url($this->parent->getRoute(), $route_params, $sort_params);
            $sort .= "<a title='$sort_title_desc' href='$url_sort' class='sort-button sort-button-down'><span class='ui-icon ui-icon-triangle-1-s'>▼</span></a>";

            $sort = "<span class='grid-sort-buttons'>$sort</span>";
            return $sort;
        }
        return '';
    }

    /**
     * @param $attributes array
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $attr
     * @param $value
     * @return Column
     */
    public function setAttr($attr, $value)
    {
        $this->attributes[$attr] = $value;
        return $this;
    }

    public function getAttr($attr, $default = null)
    {
        if (isset($this->attributes[$attr]))
            return $this->attributes[$attr];
        return $default;
    }

    /**
     * @param $attr
     * @param $value
     * @return Column
     */
    public function addAttr($attr, $value)
    {
        if (isset($this->attributes[$attr]) && is_array($this->attributes[$attr]) && in_array($value, $this->attributes[$attr]))
            return $this;
        $this->attributes[$attr][] = $value;
        return $this;
    }

    protected function makeAttrString()
    {
        $attr = array();
        foreach ($this->attributes as $at => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
                $attr[] = "$at='$value'";
            } else {
                $attr[] = "$at='$value'";
            }
        }
        if (count($attr))
            return implode(' ', $attr);
        return '';
    }
}