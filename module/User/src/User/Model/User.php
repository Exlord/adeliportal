<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 10:39 AM
 */

/*
 CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
`displayName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

namespace User\Model;

use System\Model\BaseModel;

class User extends BaseModel
{
    public $id;
    public $username;
    public $password;
    public $email;
    public $roles = array(2);
    public $displayName;
    public $emailStatus = 0;
    public $accountStatus = 0;
    public $loginDate = 0;
    public $lastLoginDate = 0;
    public $data = array();

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param int $lastLoginDate
     */
    public function setLastLoginDate($lastLoginDate)
    {
        $this->lastLoginDate = $lastLoginDate;
    }

    /**
     * @return int
     */
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    /**
     * @param int $loginDate
     */
    public function setLoginDate($loginDate)
    {
        $this->loginDate = $loginDate;
    }

    /**
     * @return int
     */
    public function getLoginDate()
    {
        return $this->loginDate;
    }

    public $filters = array('roles');

    /**
     * @param mixed $accountStatus
     */
    public function setAccountStatus($accountStatus)
    {
        $this->accountStatus = $accountStatus;
    }

    /**
     * @return mixed
     */
    public function getAccountStatus()
    {
        return $this->accountStatus;
    }

    /**
     * @param mixed $emailStatus
     */
    public function setEmailStatus($emailStatus)
    {
        $this->emailStatus = $emailStatus;
    }

    /**
     * @return mixed
     */
    public function getEmailStatus()
    {
        return $this->emailStatus;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }
}
