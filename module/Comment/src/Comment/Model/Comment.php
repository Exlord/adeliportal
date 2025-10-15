<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Koushan
 * Date: 6/19/13
 * Time: 3:18 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Comment\Model;


use System\Model\BaseModel;

class Comment extends BaseModel
{

    public $id;
    public $entityId;
    public $entityType;
    public $parentId=0;
    public $name;
    public $email;
    public $comment;
    public $status=0;
    public $userId;
    public $created;

    public function __construct()
    {
        $this->created = time();
        $this->userId = current_user()->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    public function getEntityId()
    {
        return $this->entityId;
    }

    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    }

    public function getEntityType()
    {
        return $this->entityType;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }





}