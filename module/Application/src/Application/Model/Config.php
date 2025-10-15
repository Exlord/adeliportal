<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/5/12
 * Time: 10:25 AM
 */

/*
CREATE TABLE `tbl_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `varName` varchar(200) NOT NULL,
  `varDisplayName` varchar(400) DEFAULT NULL,
  `varValue` text,
  `varGroup` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `varName` (`varName`)
) ENGINE=InnoDB 3 DEFAULT CHARSET=utf8;
 */
namespace Application\Model;

use System\Model\BaseModel;

class Config extends BaseModel
{
    public $id;
    public $varName;
    public $varDisplayName;
    public $varValue;
    public $varGroup;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setVarDisplayName($varDisplayName)
    {
        $this->varDisplayName = $varDisplayName;
    }

    public function getVarDisplayName()
    {
        return $this->varDisplayName;
    }

    public function setVarGroup($varGroup)
    {
        $this->varGroup = $varGroup;
    }

    public function getVarGroup()
    {
        return $this->varGroup;
    }

    public function setVarName($varName)
    {
        $this->varName = $varName;
    }

    public function getVarName()
    {
        return $this->varName;
    }

    public function setVarValue($varValue)
    {
        $this->varValue = $varValue;
    }

    public function getVarValue()
    {
        return $this->varValue;
    }
}
