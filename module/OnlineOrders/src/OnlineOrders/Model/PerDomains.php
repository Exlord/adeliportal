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

class PerDomains extends BaseModel
{
    public $ID;
    public $domainName;
    public $domainPrice;
    public $domainStatus;
    public $domainSell;

    public function setID($ID)
    {
        $this->ID = $ID;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
    }

    public function getDomainName()
    {
        return $this->domainName;
    }

    public function setDomainPrice($domainPrice)
    {
        $this->domainPrice = $domainPrice;
    }

    public function getDomainPrice()
    {
        return $this->domainPrice;
    }

    public function setDomainStatus($domainStatus)
    {
        $this->domainStatus = $domainStatus;
    }

    public function getDomainStatus()
    {
        return $this->domainStatus;
    }

    public function setDomainSell($domainSell)
    {
        $this->domainSell = $domainSell;
    }

    public function getDomainSell()
    {
        return $this->domainSell;
    }




}