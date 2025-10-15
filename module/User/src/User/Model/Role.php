<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/26/12
 * Time: 1:27 PM
 */
/*
 CREATE TABLE `tbl_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

namespace User\Model;

use System\Model\BaseModel;

class Role extends BaseModel
{
    public $id;
    public $roleName;
    public $locked = 0;
    public $parentId;
    public $level = RoleTable::LEVEL_MEMBER;

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }


    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    public function getLocked()
    {
        return $this->locked;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;
    }

    public function getRoleName()
    {
        return $this->roleName;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getLevel()
    {
        return $this->level;
    }


}
