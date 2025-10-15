<?php

namespace Contact\Model;

use System\Model\BaseModel;

class ContactType extends BaseModel
{
    public $id;
    public $title;
    public $contactUserId;

    /**
     * @param mixed $contactUserId
     */
    public function setContactUserId($contactUserId)
    {
        $this->contactUserId = $contactUserId;
    }

    /**
     * @return mixed
     */
    public function getContactUserId()
    {
        return $this->contactUserId;
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
