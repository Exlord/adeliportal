<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/19/13
 * Time: 12:45 PM
 */

namespace DataView\Lib;


class SharedBase
{
    /**
     * @var \Zend\View\HelperPluginManager
     */
    protected $_viewHelperManager;
    protected $_generalHelper;
    protected $_headScript;
    /**
     * @var \Zend\Mvc\Controller\Plugin\Params
     */
    protected $_paramsPlugin;
    /**
     * Renders an array into html attributes
     * @param $attr
     * @return string
     */
    protected function renderAttr($attr)
    {
        $text = '';
        foreach ($attr as $key => $value) {
            if (is_array($value))
                $value = implode(' ', $value);
            $text .= "$key='$value' ";
        }
        return $text;
    }

    protected function __construct(){
        $this->_paramsPlugin = getSM()->get('ControllerPluginManager')->get('params');
        $this->_viewHelperManager = getSM()->get('viewhelpermanager');
        $this->_generalHelper = $this->_viewHelperManager->get('general');
        $this->_headScript = $this->_viewHelperManager->get('headScript');
    }
}