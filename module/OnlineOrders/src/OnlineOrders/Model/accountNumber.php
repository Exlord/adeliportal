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

class accountNumber extends BaseModel
{

    public $ID;
    public $bankName;
    public $nameAndFamily;
    public $cardNumber;
    public $accountNumber;
    public $shebaNumber;
    public $status;

    public function setID($ID)
    {
        $this->ID = $ID;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
    }

    public function getBankName()
    {
        return $this->bankName;
    }

    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    public function setNameAndFamily($nameAndFamily)
    {
        $this->nameAndFamily = $nameAndFamily;
    }

    public function getNameAndFamily()
    {
        return $this->nameAndFamily;
    }

    public function setShebaNumber($shebaNumber)
    {
        $this->shebaNumber = $shebaNumber;
    }

    public function getShebaNumber()
    {
        return $this->shebaNumber;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }






}