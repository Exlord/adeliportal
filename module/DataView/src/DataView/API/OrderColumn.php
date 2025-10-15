<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/16/13
 * Time: 11:39 PM
 * To change this template use File | Settings | File Templates.
 */

namespace DataView\API;


class OrderColumn extends Column
{
    protected $url;
    protected $content_template = "<input class='spinner tiny_spinner update_able_order' type='text'
                                   value='%s' name='%s' data-id='%s' data-min='-999' data-max='999' data-step='1' size='5'>";

    public function __construct($name, $url, $sortable = true)
    {
        parent::__construct($name, 'Order', '50', $sortable);
        $this->setUrl($url);
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        $update_order = "<button title='%s' id='update_order' data-url='%s' class='grid_button save_button'>%s</button>";
        $update_order = sprintf($update_order, t('Save Order'), url($this->url), t('Save'));
        return sprintf($this->header_template, $this->getWidth(), $this->getSortHeader() . t($this->Header) . $update_order);
    }

    protected function getContent($data)
    {
        return sprintf($this->content_template, $data->{$this->getName()}, $this->getName(), $data->id);
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

}