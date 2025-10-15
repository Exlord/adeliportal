<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/3/13
 * Time: 1:45 PM
 */

namespace FormsManager\Model;


use System\Model\BaseModel;

class FormData extends BaseModel
{
    public $id;
    public $formId;
    public $createdTime = 0;
    public $editTime = 0;
    public $userId = 0;

    /**
     * @param mixed $editTime
     */
    public function setEditTime($editTime)
    {
        $this->editTime = $editTime;
    }

    /**
     * @return mixed
     */
    public function getEditTime()
    {
        return $this->editTime;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }


    /**
     * @param mixed $formId
     */
    public function setFormId($formId)
    {
        $this->formId = $formId;
    }

    /**
     * @return mixed
     */
    public function getFormId()
    {
        return $this->formId;
    }


    /**
     * @param mixed $createdTime
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;
    }

    /**
     * @return mixed
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
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


} 