<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */
namespace Ads\Model;

use System\Model\BaseModel;

class Ads extends BaseModel
{
    public $id;
    public $title;
    public $text;
    public $link;
    public $address;
    public $name;
    public $mail;
    public $mobile;
    public $fax;
    public $stateId;
    public $cityId;
    public $googleLatLong;
    public $smallImage;
    public $showPm;
    public $status=0;
    public $userId;
    public $createDate;
    public $expireDate;
    public $hits;
    public $baseType;
    public $secondType;
    public $regType = 0;//0=> new transfer & 1=>new Request
    public $time;
    public $starCount;
    public $finalPrice;
    public $payerStatus;
    public $editStatus;
    public $fields;
    public $catId;
    public $keyword;
    public $adType;
    public $notifyType;
   /* public $notifyType;
    public $notifyIds;*/
    public $filters = array('fields','catId','keyword','adType','notifyType');


    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $googleLatLong
     */
    public function setGoogleLatLong($googleLatLong)
    {
        $this->googleLatLong = $googleLatLong;
    }

    /**
     * @return mixed
     */
    public function getGoogleLatLong()
    {
        return $this->googleLatLong;
    }

    /**
     * @param int $regType
     */
    public function setRegType($regType)
    {
        $this->regType = $regType;
    }

    /**
     * @return int
     */
    public function getRegType()
    {
        return $this->regType;
    }

    /**
     * @param mixed $editStatus
     */
    public function setEditStatus($editStatus)
    {
        $this->editStatus = $editStatus;
    }

    /**
     * @return mixed
     */
    public function getEditStatus()
    {
        return $this->editStatus;
    }

    /**
     * @param mixed $payerStatus
     */
    public function setPayerStatus($payerStatus)
    {
        $this->payerStatus = $payerStatus;
    }

    /**
     * @return mixed
     */
    public function getPayerStatus()
    {
        return $this->payerStatus;
    }

    /**
     * @param mixed $finalPrice
     */
    public function setFinalPrice($finalPrice)
    {
        $this->finalPrice = $finalPrice;
    }

    /**
     * @return mixed
     */
    public function getFinalPrice()
    {
        return $this->finalPrice;
    }

    /**
     * @param mixed $adType
     */
    public function setAdType($adType)
    {
        $this->adType = $adType;
    }

    /**
     * @return mixed
     */
    public function getAdType()
    {
        return $this->adType;
    }

    /**
     * @param mixed $catId
     */
    public function setCatId($catId)
    {
        $this->catId = $catId;
    }

    /**
     * @return mixed
     */
    public function getCatId()
    {
        return $this->catId;
    }

    /**
     * @param mixed $keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * @return mixed
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $baseType
     */
    public function setBaseType($baseType)
    {
        $this->baseType = $baseType;
    }

    /**
     * @return mixed
     */
    public function getBaseType()
    {
        return $this->baseType;
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
     * @param mixed $createDate
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @param mixed $expireDate
     */
    public function setExpireDate($expireDate)
    {
        $this->expireDate = $expireDate;
    }

    /**
     * @return mixed
     */
    public function getExpireDate()
    {
        return $this->expireDate;
    }

    /**
     * @param mixed $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return mixed
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param mixed $hits
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
    }

    /**
     * @return mixed
     */
    public function getHits()
    {
        return $this->hits;
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
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $secondType
     */
    public function setSecondType($secondType)
    {
        $this->secondType = $secondType;
    }

    /**
     * @return mixed
     */
    public function getSecondType()
    {
        return $this->secondType;
    }

    /**
     * @param mixed $showPm
     */
    public function setShowPm($showPm)
    {
        $this->showPm = $showPm;
    }

    /**
     * @return mixed
     */
    public function getShowPm()
    {
        return $this->showPm;
    }

    /**
     * @param mixed $smallImage
     */
    public function setSmallImage($smallImage)
    {
        $this->smallImage = $smallImage;
    }

    /**
     * @return mixed
     */
    public function getSmallImage()
    {
        return $this->smallImage;
    }

    /**
     * @param mixed $starCount
     */
    public function setStarCount($starCount)
    {
        $this->starCount = $starCount;
    }

    /**
     * @return mixed
     */
    public function getStarCount()
    {
        return $this->starCount;
    }

    /**
     * @param mixed $stateId
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;
    }

    /**
     * @return mixed
     */
    public function getStateId()
    {
        return $this->stateId;
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
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }


}
