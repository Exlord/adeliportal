<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:00 PM
 */
namespace Links\Model;
/*
CREATE TABLE `tbl_links_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(200) DEFAULT NULL,
  `itemTitle` text,
  `itemLink` text,
  `itemOrder` int(11) DEFAULT NULL,
  `itemStatus` tinyint(1) DEFAULT NULL,
  `catId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

class LinksItem
{
    public $id;
    public $itemName;
    public $itemTitle;
    public $itemLink;
    public $itemOrder = 0;
    public $itemStatus = 0;
    public $catId;

    public function setCatId($catId)
    {
        $this->catId = $catId;
    }

    public function getCatId()
    {
        return $this->catId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setItemLink($itemLink)
    {
        $this->itemLink = $itemLink;
    }

    public function getItemLink()
    {
        return $this->itemLink;
    }

    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    public function getItemName()
    {
        return $this->itemName;
    }

    public function setItemOrder($itemOrder)
    {
        $this->itemOrder = $itemOrder;
    }

    public function getItemOrder()
    {
        return $this->itemOrder;
    }

    public function setItemStatus($itemStatus)
    {
        $this->itemStatus = $itemStatus;
    }

    public function getItemStatus()
    {
        return $this->itemStatus;
    }

    public function setItemTitle($itemTitle)
    {
        $this->itemTitle = $itemTitle;
    }

    public function getItemTitle()
    {
        return $this->itemTitle;
    }
}
