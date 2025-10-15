<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/16/13
 * Time: 11:39 PM
 * To change this template use File | Settings | File Templates.
 */

namespace DataView\API;


class SelectColumn extends Column
{
    private $dataTemplate;
    private $classToKey;

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
        if ($data == null)
            $data = 0;
        $class = $this->Class;
        $class[] = $this->getClassToKey($data);
        $class = $this->getClassString($class);
        $data = $this->makeSelect($data);
        return sprintf($this->template, $class, $data);
    }

    /**
     * @param mixed $classToKey
     */
    public function setClassToKey($classToKey)
    {
        $this->classToKey = $classToKey;
    }

    /**
     * @return mixed
     */
    public function getClassToKey($key)
    {
        if (is_array($this->classToKey))
            return $this->classToKey[$key];
        else
            return '';
    }

    private function makeSelect($selected)
    {
        if (is_array($this->dataTemplate)) {
            $html = "<select name='%s' id='%s'>";
            $s = '';
            foreach ($this->dataTemplate as $key => $value) {
                $value = $this->t($value);
                if ($key == $selected)
                    $s = 'selected="selected"';
                else
                    $s = '';
                $class = $this->getClassToKey($key);
                $html .= "<option class='$class' $s value='$key'>$value</option>";
            }
            $html .= "</select>";
        } else
            $html = $selected;
        return $html;
    }
}