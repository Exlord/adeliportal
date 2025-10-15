<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */
namespace Menu\Model;
use System\Model\BaseModel;

/*
 *   `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(200) DEFAULT NULL,
  `itemTitle` varchar(400) DEFAULT NULL,
  `itemUrl` varchar(500) DEFAULT NULL,
 */

/**
 * Class Menu
 * @package Menu\Model
 */
class MenuItem extends BaseModel
{
    public $id;
    public $itemName;
    public $itemTitle;
    public $parentId = 0;
    public $menuId;
    public $itemOrder;
    public $status;
    public $itemUrlType;
    public $itemUrlTypeParams = array();
    public $config = array();
    public $image;

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $itemUrlTypeParams
     */
    public function setItemUrlTypeParams($itemUrlTypeParams)
    {
        $this->itemUrlTypeParams = $itemUrlTypeParams;
    }

    /**
     * @return mixed
     */
    public function getItemUrlTypeParams()
    {
        return $this->itemUrlTypeParams;
    }


    /**
     * @param mixed $itemUrlType
     */
    public function setItemUrlType($itemUrlType)
    {
        $this->itemUrlType = $itemUrlType;
    }

    /**
     * @return mixed
     */
    public function getItemUrlType()
    {
        return $this->itemUrlType;
    }

    /**
     * @param mixed $itemOrder
     */
    public function setItemOrder($itemOrder)
    {
        $this->itemOrder = $itemOrder;
    }

    /**
     * @return mixed
     */
    public function getItemOrder()
    {
        return $this->itemOrder;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * @param mixed $menuId
     */
    public function setMenuId($menuId)
    {
        $this->menuId = $menuId;
    }

    /**
     * @return mixed
     */
    public function getMenuId()
    {
        return $this->menuId;
    }


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

    /**
     * @param mixed $itemName
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     * @return mixed
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @param mixed $itemTitle
     */
    public function setItemTitle($itemTitle)
    {
        $this->itemTitle = $itemTitle;
    }

    /**
     * @return mixed
     */
    public function getItemTitle()
    {
        return $this->itemTitle;
    }
}
