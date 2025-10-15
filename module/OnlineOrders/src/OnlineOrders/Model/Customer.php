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

class Customer extends BaseModel
{

    public $ID;
    public $idGroup;
    public $resultPrice;
    public $resultPriceLang;
    public $sumResultPrice;
    public $itemCustomer;
    public $langCustomer;
    public $others;
    public $date;
    public $supportPer;
    public $namePer;
    public $nameCompanyPer;
    public $emailPer;
    public $phonePer;
    public $mobilePer;
    public $addressPer;
    public $commentPer;
    public $domainName;
    public $domainType;
    public $typePayment;
    public $infoPayment;
    public $end4CardNumber;
    public $datePayment;
    public $seryalPayment;
    public $factorNumber;
    public $refCode;
    public $confirmation;

    public function setID($ID)
    {
        $this->ID = $ID;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function setAddressPer($addressPer)
    {
        $this->addressPer = $addressPer;
    }

    public function getAddressPer()
    {
        return $this->addressPer;
    }

    public function setCommentPer($commentPer)
    {
        $this->commentPer = $commentPer;
    }

    public function getCommentPer()
    {
        return $this->commentPer;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDatePayment($datePayment)
    {
        $this->datePayment = $datePayment;
    }

    public function getDatePayment()
    {
        return $this->datePayment;
    }

    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
    }

    public function getDomainName()
    {
        return $this->domainName;
    }

    public function setDomainType($domainType)
    {
        $this->domainType = $domainType;
    }

    public function getDomainType()
    {
        return $this->domainType;
    }

    public function setEmailPer($emailPer)
    {
        $this->emailPer = $emailPer;
    }

    public function getEmailPer()
    {
        return $this->emailPer;
    }

    public function setEnd4CardNumber($end4CardNumber)
    {
        $this->end4CardNumber = $end4CardNumber;
    }

    public function getEnd4CardNumber()
    {
        return $this->end4CardNumber;
    }

    public function setFactorNumber($factorNumber)
    {
        $this->factorNumber = $factorNumber;
    }

    public function getFactorNumber()
    {
        return $this->factorNumber;
    }

    public function setIdGroup($idGroup)
    {
        $this->idGroup = $idGroup;
    }

    public function getIdGroup()
    {
        return $this->idGroup;
    }

    public function setInfoPayment($infoPayment)
    {
        $this->infoPayment = $infoPayment;
    }

    public function getInfoPayment()
    {
        return $this->infoPayment;
    }

    public function setItemCustomer($itemCustomer)
    {
        $this->itemCustomer = $itemCustomer;
    }

    public function getItemCustomer()
    {
        return $this->itemCustomer;
    }

    public function setLangCustomer($langCustomer)
    {
        $this->langCustomer = $langCustomer;
    }

    public function getLangCustomer()
    {
        return $this->langCustomer;
    }

    public function setMobilePer($mobilePer)
    {
        $this->mobilePer = $mobilePer;
    }

    public function getMobilePer()
    {
        return $this->mobilePer;
    }

    public function setNameCompanyPer($nameCompanyPer)
    {
        $this->nameCompanyPer = $nameCompanyPer;
    }

    public function getNameCompanyPer()
    {
        return $this->nameCompanyPer;
    }

    public function setNamePer($namePer)
    {
        $this->namePer = $namePer;
    }

    public function getNamePer()
    {
        return $this->namePer;
    }

    public function setOthers($others)
    {
        $this->others = $others;
    }

    public function getOthers()
    {
        return $this->others;
    }

    public function setPhonePer($phonePer)
    {
        $this->phonePer = $phonePer;
    }

    public function getPhonePer()
    {
        return $this->phonePer;
    }

    public function setRefCode($refCode)
    {
        $this->refCode = $refCode;
    }

    public function getRefCode()
    {
        return $this->refCode;
    }

    public function setResultPrice($resultPrice)
    {
        $this->resultPrice = $resultPrice;
    }

    public function getResultPrice()
    {
        return $this->resultPrice;
    }

    public function setResultPriceLang($resultPriceLang)
    {
        $this->resultPriceLang = $resultPriceLang;
    }

    public function getResultPriceLang()
    {
        return $this->resultPriceLang;
    }

    public function setSeryalPayment($seryalPayment)
    {
        $this->seryalPayment = $seryalPayment;
    }

    public function getSeryalPayment()
    {
        return $this->seryalPayment;
    }

    public function setSumResultPrice($sumResultPrice)
    {
        $this->sumResultPrice = $sumResultPrice;
    }

    public function getSumResultPrice()
    {
        return $this->sumResultPrice;
    }

    public function setSupportPer($supportPer)
    {
        $this->supportPer = $supportPer;
    }

    public function getSupportPer()
    {
        return $this->supportPer;
    }

    public function setTypePayment($typePayment)
    {
        $this->typePayment = $typePayment;
    }

    public function getTypePayment()
    {
        return $this->typePayment;
    }

    public function setConfirmation($confirmation)
    {
        $this->confirmation = $confirmation;
    }

    public function getConfirmation()
    {
        return $this->confirmation;
    }




}