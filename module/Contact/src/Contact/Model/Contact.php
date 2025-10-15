<?php

namespace Contact\Model;

use System\Model\BaseModel;

class Contact extends BaseModel
{
    public $id;
    public $sendId;
    public $name;
    public $email;
    public $mobile;
    public $description;
    public $typeContact;
    public $date;
    public $status = 0;

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
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
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
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
     * @param mixed $sendId
     */
    public function setSendId($sendId)
    {
        $this->sendId = $sendId;
    }

    /**
     * @return mixed
     */
    public function getSendId()
    {
        return $this->sendId;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $typeContact
     */
    public function setTypeContact($typeContact)
    {
        $this->typeContact = $typeContact;
    }

    /**
     * @return mixed
     */
    public function getTypeContact()
    {
        return $this->typeContact;
    }



}
