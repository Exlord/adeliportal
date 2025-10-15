<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:00 PM
 */
namespace Category\Model;
/*
 CREATE TABLE `tbl_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catName` varchar(50) DEFAULT NULL,
  `catText` text,
  `catEnabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

class Category
{
    public $id;
    public $catName;
    public $catText;
    public $catMachineName;

    public function setCatMachineName($catMachineName)
    {
        $this->catMachineName = $catMachineName;
    }

    public function getCatMachineName()
    {
        return $this->catMachineName;
    }

    public function setCatName($catName)
    {
        $this->catName = $catName;
    }

    public function getCatName()
    {
        return $this->catName;
    }

    public function setCatText($catText)
    {
        $this->catText = $catText;
    }

    public function getCatText()
    {
        return $this->catText;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}
