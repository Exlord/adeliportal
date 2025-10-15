<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */

/*`id` int(11) NOT NULL AUTO_INCREMENT,
  `cityTitle` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `stateId` int(11) NOT NULL,*/

namespace GeographicalAreas\Model;
class City
{
    public $id;
    public $cityTitle;
    public $itemStatus;
    public $stateId;
    public $itemOrder;

    public function setItemOrder($itemOrder)
    {
        $this->itemOrder = $itemOrder;
    }

    public function getItemOrder()
    {
        return $this->itemOrder;
    }

    public function setCityTitle($cityTitle)
    {
        $this->cityTitle = $cityTitle;
    }

    public function getCityTitle()
    {
        return $this->cityTitle;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setStateId($stateId)
    {
        $this->stateId = $stateId;
    }

    public function getStateId()
    {
        return $this->stateId;
    }

    public function setItemStatus($statue)
    {
        $this->itemStatus = $statue;
    }

    public function getItemStatus()
    {
        return $this->itemStatus;
    }
}
