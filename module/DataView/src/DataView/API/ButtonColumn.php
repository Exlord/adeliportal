<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/16/13
 * Time: 11:39 PM
 * To change this template use File | Settings | File Templates.
 */

namespace DataView\API;


class ButtonColumn extends LinkColumn
{
    const BUTTON_EDIT = 'edit_button';
    const BUTTON_DELETE = 'delete_button';
    const BUTTON_SEARCH = 'search_button';

    protected $buttonType;

    public function __construct($name, $type, $url)
    {
        $this->setButtonType($type);
        parent::__construct($name, '', $url);
        $this->setWidth('20');
    }

    protected function init()
    {
        $this->addAttr('class', 'button-column');
        $this->addLinkClass('grid_button');
        $this->setAttr('align', 'center');
        $this->addLinkClass($this->buttonType);
    }

    protected function getData($data)
    {
        return t($this->getName());
    }

    /**
     * @param mixed $buttonType
     * @return ButtonColumn
     */
    public function setButtonType($buttonType)
    {
        $this->buttonType = $buttonType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getButtonType()
    {
        return $this->buttonType;
    }

    protected function makeUrl($data)
    {
        $params = $this->url_params;
        $params['id'] = $data->id;
        $url = url($this->getUrl(), $params, $this->route_options);
        return $url;
    }

}