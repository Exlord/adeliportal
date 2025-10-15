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

class Groups extends BaseModel
{

    public $id;
    public $groupName;
    public $groupDesc;
    public $groupPosition;
    public $groupParentId;
    public $groupPermit;
    public $imageIcon;
    public $groupLevel;
    public $groupPrice;
    public $groupShowLang;
    public $groupShowSupport;





    public function setGroupPosition($groupPosition)
    {
        $this->groupPosition = $groupPosition;
    }

    public function getGroupPosition()
    {
        return $this->groupPosition;
    }


    public function setGroupDesc($groupDesc)
    {
        $this->groupDesc = $groupDesc;
    }

    public function getGroupDesc()
    {
        return $this->groupDesc;
    }

    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    public function getGroupName()
    {
        return $this->groupName;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setGroupParentId($groupParentId)
    {
        $this->groupParentId = $groupParentId;
    }

    public function getGroupParentId()
    {
        return $this->groupParentId;
    }



    public function setGroupPermit($groupPermit)
    {
        $this->groupPermit = $groupPermit;
    }

    public function getGroupPermit()
    {
        return $this->groupPermit;
    }

    public function setImageIcon($imageIcon)
    {
        $this->imageIcon = $imageIcon;
    }

    public function getImageIcon()
    {
        return $this->imageIcon;
    }

    public function setGroupLevel($groupLevel)
    {
        $this->groupLevel = $groupLevel;
    }

    public function getGroupLevel()
    {
        return $this->groupLevel;
    }

    public function setGroupPrice($groupPrice)
    {
        $this->groupPrice = $groupPrice;
    }

    public function getGroupPrice()
    {
        return $this->groupPrice;
    }


    public function setGroupShowLang($groupShowLang)
    {
        $this->groupShowLang = $groupShowLang;
    }

    public function getGroupShowLang()
    {
        return $this->groupShowLang;
    }

    public function setGroupShowSupport($groupShowSupport)
    {
        $this->groupShowSupport = $groupShowSupport;
    }

    public function getGroupShowSupport()
    {
        return $this->groupShowSupport;
    }




}