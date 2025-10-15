<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/24/13
 * Time: 11:02 AM
 */

namespace Application\Model;


use System\Model\BaseModel;

class Template extends BaseModel
{
    public $id;
    public $title;
    public $format;

    /**
     * @param mixed $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }


} 