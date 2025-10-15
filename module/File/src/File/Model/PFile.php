<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/8/2014
 * Time: 1:46 PM
 */

namespace File\Model;


use System\Model\BaseModel;

class PFile extends BaseModel
{
    public $id;
    public $name;
    public $title;
    public $downloadAs;
    public $path;
    public $accessibility = array();

    /**
     * @return mixed
     */
    public function getDownloadAs()
    {
        return $this->downloadAs;
    }

    /**
     * @param mixed $downloadAs
     */
    public function setDownloadAs($downloadAs)
    {
        $this->downloadAs = $downloadAs;
    }

    /**
     * @return array
     */
    public function getAccessibility()
    {
        return $this->accessibility;
    }

    /**
     * @param array $accessibility
     */
    public function setAccessibility($accessibility)
    {
        $this->accessibility = $accessibility;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


} 