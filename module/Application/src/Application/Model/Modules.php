<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/19/13
 * Time: 10:12 AM
 */

namespace Application\Model;


use System\Model\BaseModel;

class Modules extends BaseModel
{
    public $id;
    public $name;
    public $dbVersion;

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
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $dbVersion
     */
    public function setDbVersion($dbVersion)
    {
        $this->dbVersion = $dbVersion;
    }

    /**
     * @return mixed
     */
    public function getDbVersion()
    {
        return $this->dbVersion;
    }
}