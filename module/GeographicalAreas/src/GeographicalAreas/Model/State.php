<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */

/*
   `id` int(11) NOT NULL AUTO_INCREMENT,
  `stateTitle` varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  `countryId` int(11) DEFAULT NULL,
 */

namespace GeographicalAreas\Model;
class State
{
    public $id;
    public $stateTitle;
    public $itemStatus;
    public $countryId;
    public $itemOrder;

    public function setItemOrder($itemOrder)
    {
        $this->itemOrder = $itemOrder;
    }

    public function getItemOrder()
    {
        return $this->itemOrder;
    }

    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
    }

    public function getCountryId()
    {
        return $this->countryId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setStateTitle($stateTitle)
    {
        $this->stateTitle = $stateTitle;
    }

    public function getStateTitle()
    {
        return $this->stateTitle;
    }

    public function setItemStatus($status)
    {
        $this->itemStatus = $status;
    }

    public function getItemStatus()
    {
        return $this->itemStatus;
    }
}
