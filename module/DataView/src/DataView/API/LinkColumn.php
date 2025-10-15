<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/16/13
 * Time: 11:39 PM
 * To change this template use File | Settings | File Templates.
 */

namespace DataView\API;


class LinkColumn extends Column
{
    protected $url;
    protected $url_params = array();
    protected $route_options = array();
    protected $content_template = "<a class='%s' title='%s' href='%s'>%s</a>";
    protected $link_class = array();
    protected $content;

    public function __construct($name, $header, $url, $content = '')
    {
        $this->setUrl($url);
        $this->content = $content;
        parent::__construct($name, $header, '', false);
    }

    protected function init()
    {
        $this->addAttr('class', 'link-column');
    }

    protected function getData($data)
    {
        return $data->{$this->getName()};
    }

    protected function getContent($data)
    {
        if (isset($data->locked) && $data->locked) {
            return 'locked';
        }

        if (is_closure($this->url)) {
            $url = $this->url;
            $href = $url($data);
        } else {
            $href = $this->makeUrl($data);
        }

        $class = implode(' ', $this->link_class);
        if (is_closure($this->content)) {
            $content = $this->content;
            $content = $content($data);
        } else
            $content = $this->getData($data);

        return sprintf($this->content_template, $class, t($this->getName()), $href, $content);
    }

    /**
     * @param mixed $url
     * @return ButtonColumn
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function setRouteOptions($route_options)
    {
        $this->route_options = $route_options;
    }

    public function getRouteOptions()
    {
        return $this->route_options;
    }

    public function setUrlParams($url_params)
    {
        $this->url_params = $url_params;
    }

    public function getUrlParams()
    {
        return $this->url_params;
    }

    protected function makeUrl($data)
    {
        return url($this->getUrl(), $this->url_params, $this->route_options);
    }

    public function setLinkClass($link_class)
    {
        $this->link_class = $link_class;
    }

    public function getLinkClass()
    {
        return $this->link_class;
    }

    public function addLinkClass($class)
    {
        if (!in_array($class, $this->link_class))
            $this->link_class[] = $class;
    }
}