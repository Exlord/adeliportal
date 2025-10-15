<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */

/*`id` int(11) NOT NULL AUTO_INCREMENT,
  `countryTitle` varchar(200) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',*/

namespace GeographicalAreas\Model;
class Country
{
    public $id;
    public $countryTitle;
    public $itemStatus;
    public $itemOrder;

    public function setItemOrder($itemOrder)
    {
        $this->itemOrder = $itemOrder;
    }

    public function getItemOrder()
    {
        return $this->itemOrder;
    }

    public function setCountryTitle($countryTitle)
    {
        $this->countryTitle = $countryTitle;
    }

    public function getCountryTitle()
    {
        return $this->countryTitle;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
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
