<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/13/13
 * Time: 2:34 PM
 */

namespace DataView\Lib;


class Button extends Column
{
    /**
     * @var callable
     */
    private $closure;
    protected $_buttonTemplate = "<a %s>%s</a>";
    protected $_iconTemplate = "<i class='%s'></i>";
    public $route = null;
    public $routeParams = array();
    public $routeOptions = array();
    public $text;
    public $icon;

    protected function _configRoute()
    {
        $this->configure();
        if (!$this->route) {
            $this->route = $this->_dataGrid->route;
            $this->routeParams = array_merge_recursive($this->routeParams, $this->_dataGrid->routeParams);
//            $this->routeOptions = array_merge_recursive($this->routeOptions, $this->_dataGrid->routeOptions);
        }
    }

    /**
     * @param $title
     * @param null $closure
     * @param array $options
     */
    public function __construct($title, $closure = null, array $options = array())
    {
        $options['contentAttr']['class'][] = 'btn';
        $options['contentAttr']['class'][] = 'btn-xs';
        if (!isset($options['headerAttr']['width']))
            $options['headerAttr']['width'] = '30px';
        $options['headerAttr']['align'] = 'center';
        $options['attr']['align'] = 'center';
        if (isset($options['contentAttr']) && isset($options['contentAttr']['icon'])) {
            $this->icon = $options['contentAttr']['icon'];
        }
        parent::__construct($title, $title, $options);
        $this->sortable = false;
        $this->hasTextFilter = false;
        $this->closure = $closure;
    }

    private function configure()
    {
        if (is_closure($this->closure)) {
            $c = $this->closure;
            $c($this);
        }
    }

    public function getValue()
    {
        if ($this->isRowLocked())
            return $this->renderLockedColumn();
        $this->_configRoute();

        if ($this->route[0] != '#')
            $this->contentAttr['href'] = url($this->route, $this->routeParams, $this->routeOptions);
        else
            $this->contentAttr['href'] = $this->route;

        $this->contentAttr['title'] = $this->getTitle();
        $this->contentAttr['data-id'] = $this->_dataGrid->getIdCell()->getValue();
        if (empty($this->text))
            $this->text = $this->getTitle();
        if (!empty($this->icon))
            $this->text = sprintf($this->_iconTemplate, $this->icon);
        return sprintf($this->_buttonTemplate, $this->renderAttr($this->contentAttr), $this->text);
    }
}