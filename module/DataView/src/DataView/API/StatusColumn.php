<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/16/13
 * Time: 11:39 PM
 * To change this template use File | Settings | File Templates.
 */

namespace DataView\API;


class StatusColumn extends Column
{
    private $dataTemplate;
    private $classToKey;

    protected function init(){
        $this->addClass('status-column');
    }

    /**
     * @param mixed $dataTemplate
     * @return SelectColumn
     */
    public function setDataTemplate($dataTemplate)
    {
        $this->dataTemplate = $dataTemplate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataTemplate()
    {
        return $this->dataTemplate;
    }

    public function render($data)
    {
        $data = $data->{$this->getName()};
        if ($data == null)
            $data = '0';
        $class = $this->Class;
        $class[] = 'status-' . str_replace(' ', '-', strtolower($this->dataTemplate[$data]));
        $class = $this->getClassString($class);
        $attr = ' title="' . $this->t($this->dataTemplate[$data]) . '"';
        $attr .= $class;
        $data = "<span>" . $this->t($this->dataTemplate[$data]) . "</span>";
        return sprintf($this->template, $attr, $data);
    }
}