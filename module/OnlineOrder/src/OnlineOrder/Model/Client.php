<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/17/12
 * Time: 11:13 AM
 * To change this template use File | Settings | File Templates.
 */

/*
CREATE TABLE `tbl_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientName` varchar(200) DEFAULT NULL,
  `clientEmail` varchar(200) DEFAULT NULL,
  `clientDomain` varchar(200) DEFAULT NULL,
  `dbName` varchar(100) DEFAULT NULL,
  `dbUser` varchar(100) DEFAULT NULL,
  `dbPass` varchar(100) DEFAULT NULL,
  `diskSpace` int(11) DEFAULT NULL COMMENT 'used disk space in MB',
  `bandwidth` int(11) DEFAULT NULL COMMENT 'used bandwidth over a month in MB',
  `locked` tinyint(1) DEFAULT '0',
 `modules` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientDomain` (`clientDomain`)
) ENGINE=InnoDB 4 DEFAULT CHARSET=utf8;
 */


namespace OnlineOrder\Model;

class Client
{
    public $id;
    public $clientName;
    public $clientDomain;
    public $clientEmail;
    public $dbName;
    public $dbUser;
    public $dbPass;
    public $diskSpace;
    public $bandwidth;
    public $locked;
    public $modules;
    public $username;
    public $password;
    public $subDomainUser;
    public $subDomainPass;

    /**
     * @param mixed $subDomainPass
     */
    public function setSubDomainPass($subDomainPass)
    {
        $this->subDomainPass = $subDomainPass;
    }

    /**
     * @return mixed
     */
    public function getSubDomainPass()
    {
        return $this->subDomainPass;
    }

    /**
     * @param mixed $subDomainUser
     */
    public function setSubDomainUser($subDomainUser)
    {
        $this->subDomainUser = $subDomainUser;
    }

    /**
     * @return mixed
     */
    public function getSubDomainUser()
    {
        return $this->subDomainUser;
    }

    public function setBandwidth($bandwidth)
    {
        $this->bandwidth = $bandwidth;
    }

    public function getBandwidth()
    {
        return $this->bandwidth;
    }

    public function setClientDomain($clientDomain)
    {
        $this->clientDomain = $clientDomain;
    }

    public function getClientDomain()
    {
        return $this->clientDomain;
    }

    public function setClientEmail($clientEmail)
    {
        $this->clientEmail = $clientEmail;
    }

    public function getClientEmail()
    {
        return $this->clientEmail;
    }

    public function setClientName($clientName)
    {
        $this->clientName = $clientName;
    }

    public function getClientName()
    {
        return $this->clientName;
    }

    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }

    public function getDbName()
    {
        return $this->dbName;
    }

    public function setDbPass($dbPass)
    {
        $this->dbPass = $dbPass;
    }

    public function getDbPass()
    {
        return $this->dbPass;
    }

    public function setDbUser($dbUser)
    {
        $this->dbUser = $dbUser;
    }

    public function getDbUser()
    {
        return $this->dbUser;
    }

    public function setDiskSpace($diskSpace)
    {
        $this->diskSpace = $diskSpace;
    }

    public function getDiskSpace()
    {
        return $this->diskSpace;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    public function getLocked()
    {
        return $this->locked;
    }

    public function setModules($modules)
    {
        $this->modules = $modules;
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
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