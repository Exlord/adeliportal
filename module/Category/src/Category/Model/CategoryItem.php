<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:00 PM
 */
namespace Category\Model;

/*
 CREATE TABLE `tbl_category_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(50) DEFAULT NULL,
  `itemText` text,
  `itemIcon` text,
  `itemParent` int(11) DEFAULT '0',
  `itemEnabled` tinyint(1) DEFAULT '0',
  `catId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

use System\Model\BaseModel;

class CategoryItem extends BaseModel
{
    public $id;
    public $itemName;
    public $itemText;
    public $parentId = 0;
    public $itemStatus = 0;
    public $catId;
    public $itemOrder = 0;
    public $image;
    public $filters = array('image');

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setItemOrder($itemOrder)
    {
        $this->itemOrder = $itemOrder;
    }

    public function getItemOrder()
    {
        return $this->itemOrder;
    }

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

    public function setItemStatus($itemEnabled)
    {
        $this->itemStatus = $itemEnabled;
    }

    public function getItemStatus()
    {
        return $this->itemStatus;
    }

    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    public function getItemName()
    {
        return $this->itemName;
    }

    public function setParentId($itemParent)
    {
        $this->parentId = $itemParent;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setItemText($itemText)
    {
        $this->itemText = $itemText;
    }

    public function getItemText()
    {
        return $this->itemText;
    }
}
