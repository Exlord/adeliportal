<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */

namespace RealEstate\Model;

use System\Model\BaseModel;

class RealEstate extends BaseModel
{
    public $id;
    public $ownerName;
    public $ownerPhone;
    public $ownerMobile;
    public $ownerEmail;
    public $addressShort;
    public $addressFull;
    public $description;
    public $mortgagePrice=0;
    public $totalPrice=0;
    public $priceOneMeter=0;
    public $rentalPrice=0;
    public $regType;
    public $estateType;
    public $isSpecial;
    public $showInfo = 0;
    public $expireShowInfo = 0;
    public $expireSpecial = 0;
    public $newArea;
    public $areaId;
    public $cityId;
    public $stateId;
    public $userId;
    public $isRequest = 0;
    public $status = 0;
    public $created;
    public $published;
    public $modified;
    public $expire;
    public $version;
    public $localVersion;
    public $transferFields = array();
    public $images;
    public $googleLatLong=0;
    public $viewCounter = 0;
    public $pm;
    public $app=0;

    public function setApp($app)
    {
        $this->app = $app;
    }

    public function getApp()
    {
        return $this->app;
    }
    /**
     * @param mixed $newArea
     */
    public function setNewArea($newArea)
    {
        $this->newArea = $newArea;
    }

    /**
     * @return mixed
     */
    public function getNewArea()
    {
        return $this->newArea;
    }

    /**
     * @param mixed $areaId
     */
    public function setAreaId($areaId)
    {
        $this->areaId = $areaId;
    }

    /**
     * @return mixed
     */
    public function getAreaId()
    {
        return $this->areaId;
    }

    /**
     * @param null $expireShowInfo
     */
    public function setExpireShowInfo($expireShowInfo)
    {
        $this->expireShowInfo = $expireShowInfo;
    }

    /**
     * @return null
     */
    public function getExpireShowInfo()
    {
        return $this->expireShowInfo;
    }

    /**
     * @param null $expireSpecial
     */
    public function setExpireSpecial($expireSpecial)
    {
        $this->expireSpecial = $expireSpecial;
    }

    /**
     * @return null
     */
    public function getExpireSpecial()
    {
        return $this->expireSpecial;
    }

    /**
     * @param int $showInfo
     */
    public function setShowInfo($showInfo)
    {
        $this->showInfo = $showInfo;
    }

    /**
     * @return int
     */
    public function getShowInfo()
    {
        return $this->showInfo;
    }


    public function setPriceOneMeter($priceOneMeter)
    {
        $this->priceOneMeter = $priceOneMeter;
    }

    public function getPriceOneMeter()
    {
        return $this->priceOneMeter;
    }

    public $filters = array('images', 'transferFields');

    /**
     * @param array $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $addressFull
     */
    public function setAddressFull($addressFull)
    {
        $this->addressFull = $addressFull;
    }

    /**
     * @return mixed
     */
    public function getAddressFull()
    {
        return $this->addressFull;
    }

    /**
     * @param mixed $addressShort
     */
    public function setAddressShort($addressShort)
    {
        $this->addressShort = $addressShort;
    }

    /**
     * @return mixed
     */
    public function getAddressShort()
    {
        return $this->addressShort;
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
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $estateType
     */
    public function setEstateType($estateType)
    {
        $this->estateType = $estateType;
    }

    /**
     * @return mixed
     */
    public function getEstateType()
    {
        return $this->estateType;
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
     * @param mixed $isRequest
     */
    public function setIsRequest($isRequest)
    {
        $this->isRequest = $isRequest;
    }

    /**
     * @return mixed
     */
    public function getIsRequest()
    {
        return $this->isRequest;
    }

    /**
     * @param mixed $mortgagePrice
     */
    public function setMortgagePrice($mortgagePrice)
    {
        $this->mortgagePrice = $mortgagePrice;
    }

    /**
     * @return mixed
     */
    public function getMortgagePrice()
    {
        return $this->mortgagePrice;
    }

    /**
     * @param mixed $ownerEmail
     */
    public function setOwnerEmail($ownerEmail)
    {
        $this->ownerEmail = $ownerEmail;
    }

    /**
     * @return mixed
     */
    public function getOwnerEmail()
    {
        return $this->ownerEmail;
    }

    /**
     * @param mixed $ownerMobile
     */
    public function setOwnerMobile($ownerMobile)
    {
        $this->ownerMobile = $ownerMobile;
    }

    /**
     * @return mixed
     */
    public function getOwnerMobile()
    {
        return $this->ownerMobile;
    }

    /**
     * @param mixed $ownerName
     */
    public function setOwnerName($ownerName)
    {
        $this->ownerName = $ownerName;
    }

    /**
     * @return mixed
     */
    public function getOwnerName()
    {
        return $this->ownerName;
    }

    /**
     * @param mixed $ownerPhone
     */
    public function setOwnerPhone($ownerPhone)
    {
        $this->ownerPhone = $ownerPhone;
    }

    /**
     * @return mixed
     */
    public function getOwnerPhone()
    {
        return $this->ownerPhone;
    }

    /**
     * @param mixed $regType
     */
    public function setRegType($regType)
    {
        $this->regType = $regType;
    }

    /**
     * @return mixed
     */
    public function getRegType()
    {
        return $this->regType;
    }

    /**
     * @param mixed $isSpecial
     */
    public function setIsSpecial($isSpecial)
    {
        $this->isSpecial = $isSpecial;
    }

    /**
     * @return mixed
     */
    public function getIsSpecial()
    {
        return $this->isSpecial;
    }

    /**
     * @param mixed $rentalPrice
     */
    public function setRentalPrice($rentalPrice)
    {
        $this->rentalPrice = $rentalPrice;
    }

    /**
     * @return mixed
     */
    public function getRentalPrice()
    {
        return $this->rentalPrice;
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
     * @param mixed $totalPrice
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
    }

    /**
     * @return mixed
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param array $transferFields
     */
    public function setTransferFields($transferFields)
    {
        $this->transferFields = $transferFields;
    }

    /**
     * @return array
     */
    public function getTransferFields()
    {
        return $this->transferFields;
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

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $expire
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
    }

    /**
     * @return mixed
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * @param mixed $localVersion
     */
    public function setLocalVersion($localVersion)
    {
        $this->localVersion = $localVersion;
    }

    /**
     * @return mixed
     */
    public function getLocalVersion()
    {
        return $this->localVersion;
    }

    /**
     * @param mixed $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function setGoogleLatLong($googleLatLong)
    {
        $this->googleLatLong = $googleLatLong;
    }

    public function getGoogleLatLong()
    {
        return $this->googleLatLong;
    }

    /**
     * @param mixed $pm
     */
    public function setPm($pm)
    {
        $this->pm = $pm;
    }

    /**
     * @return mixed
     */
    public function getPm()
    {
        return $this->pm;
    }

    /**
     * @param mixed $viewCounter
     */
    public function setViewCounter($viewCounter)
    {
        $this->viewCounter = $viewCounter;
    }

    /**
     * @return mixed
     */
    public function getViewCounter()
    {
        return $this->viewCounter;
    }


}
