<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/23/2014
 * Time: 11:06 AM
 */

namespace Application\API;


use System\Controller\BaseAbstractActionController;
use Theme\API\Common;

class Widgets
{
    private $controller;
    public $data = array();

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return BaseAbstractActionController
     */
    public function getController()
    {
        return $this->controller;
    }

    public function render($view)
    {
        return $this->getController()->render($view);
    }

    public function getAction($controllerName, $action = 'index', $params = array())
    {
        $params['action'] = $action;
        return $this->render($this->getController()->forward()->dispatch($controllerName, $params));
    }

    public function wrap($data, $class = 'col-md-12', $attributes = array())
    {
        if (strpos($class, 'col') !== false)
            $data = "<div class='box box-primary dynamic-widget'><div class='box-body'>{$data}</div></div>";

        $attributes['class'] = $class;
        $attributes = Common::Attributes($attributes);
        $data = "<div {$attributes}>{$data}</div>";

        return $data;
    }
} 