<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/26/12
 * Time: 1:27 PM
 */
/*
CREATE TABLE `tbl_users_roles` (
  `userId` int(11) DEFAULT NULL,
  `roleId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

namespace User\Model;
class UserRole
{
    public $userId;
    public $roleId;
    public $roleName;
    public $id;

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

    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    public function getRoleId()
    {
        return $this->roleId;
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
