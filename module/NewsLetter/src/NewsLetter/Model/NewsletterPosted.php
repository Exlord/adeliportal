<?php

namespace NewsLetter\Model;

use System\Model\BaseModel;

class NewsletterPosted extends BaseModel
{

    public $id;
    public $entityType;
    public $entityId;
    public $lastId;

    /**
     * @param mixed $entityId
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param mixed $entityType
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->entityType;
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
     * @param mixed $lastId
     */
    public function setLastId($lastId)
    {
        $this->lastId = $lastId;
    }

    /**
     * @return mixed
     */
    public function getLastId()
    {
        return $this->lastId;
    }



}
