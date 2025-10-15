<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Koushan
 * Date: 6/19/13
 * Time: 3:18 PM
 * To change this template use File | Settings | File Templates.
 */

namespace OnlineOrders\Model;


use System\Model\BaseModel;

class Items extends BaseModel
{

    public $id;
    public $itemName;
    public $itemDesc;
    public $itemPrice;
    public $itemType;
    public $itemActive;
    public $itemPosition;
    public $itemDescMore;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setItemActive($itemActive)
    {
        $this->itemActive = $itemActive;
    }

    public function getItemActive()
    {
        return $this->itemActive;
    }

    public function setItemDesc($itemDesc)
    {
        $this->itemDesc = $itemDesc;
    }

    public function getItemDesc()
    {
        return $this->itemDesc;
    }

    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    public function getItemName()
    {
        return $this->itemName;
    }

    public function setItemPosition($itemPosition)
    {
        $this->itemPosition = $itemPosition;
    }

    public function getItemPosition()
    {
        return $this->itemPosition;
    }

    public function setItemPrice($itemPrice)
    {
        $this->itemPrice = $itemPrice;
    }

    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }

    public function getItemType()
    {
        return $this->itemType;
    }

    public function setItemDescMore($itemDescMore)
    {
        $this->itemDescMore = $itemDescMore;
    }

    public function getItemDescMore()
    {
        return $this->itemDescMore;
    }





}