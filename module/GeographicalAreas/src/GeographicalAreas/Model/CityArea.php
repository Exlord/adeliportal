<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */

namespace GeographicalAreas\Model;
class CityArea
{
    public $id;
    public $areaTitle;
    public $itemStatus;
    public $cityId;
    public $itemOrder;
    public $parentId;

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
     * @param mixed $areaTitle
     */
    public function setAreaTitle($areaTitle)
    {
        $this->areaTitle = $areaTitle;
    }

    /**
     * @return mixed
     */
    public function getAreaTitle()
    {
        return $this->areaTitle;
    }

    /**
     * @param mixed $cityId
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
    }

    /**
     * @return mixed
     */
    public function getCityId()
    {
        return $this->cityId;
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
     * @param mixed $itemStatus
     */
    public function setItemStatus($itemStatus)
    {
        $this->itemStatus = $itemStatus;
    }

    /**
     * @return mixed
     */
    public function getItemStatus()
    {
        return $this->itemStatus;
    }


}
