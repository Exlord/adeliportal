<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/16/13
 * Time: 10:59 AM
 */

namespace DataView\Lib;


use Theme\API\Common;

class GridButton
{
    private $_template = "<a %s>%s</a>";

    public $title = '';
    public $label = 'Button';
    public $route;
    public $class = array('toolbar-button', 'btn', 'btn-sm');
    public $iconOnly = false; //icon_button
    public $id;
    public $routeParams;
    public $routeOptions;
    public $attributes = array();
    public $icon;

    public function __construct($label, $title, $id, $iconOnly, $route, $routeParams = array(), $routeOptions = array(), $attributes = array())
    {
        $this->title = $title;
        $this->label = $label;
        $this->id = $id;
        $this->iconOnly = $iconOnly;
        $this->route = $route;
        $this->routeParams = $routeParams;
        $this->routeOptions = $routeOptions;
        $this->attributes = $attributes;
    }

    public function render($index)
    {
//        if ($this->iconOnly)
//            $this->class[] = 'icon_button';
        if (!($btnClass = array_search('btn-', $this->class)))
            $this->class[] = 'btn-default';
        $class = implode(' ', $this->class);
        $this->label = trim($this->label);
        if (!empty($this->label))
            $this->label = t($this->label);
        $this->title = trim($this->title);
        if (!empty($this->title))
            $this->title = t($this->title);
        $id = $this->id ? $this->id : 'toolbar_button_' . $index;
        $this->attributes['id'] = $id;
        $this->attributes['title'] = $this->title;
        $this->attributes['class'] = $class;
        $this->attributes['href'] = url($this->route, $this->routeParams, $this->routeOptions);

        if(empty($this->icon))
            $this->icon = 'glyphicon glyphicon-cog';
        if (!empty($this->icon)) {
            $this->icon = "<span class='{$this->icon}'></span>";
            if ($this->iconOnly)
                $this->label = $this->icon;
            else
                $this->label = $this->icon . $this->label;
        }

        return sprintf($this->_template, Common::Attributes($this->attributes), $this->label);
    }
}