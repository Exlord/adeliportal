<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/3/14
 * Time: 9:16 AM
 */

namespace EducationalCenter\Model;


use System\Model\BaseModel;

class WorkshopClass extends BaseModel
{
    public $id;
    public $workshopId = 0;
    public $educatorId = 0;
    public $title;
    public $note;
    public $capacity = 1;
    public $price = 0;
    public $status = 0;
    public $location;
    public $phone;

    //region External Fields
    public $username;
    public $displayName;
    public $firstName;
    public $lastName;
    public $firstSession;
    public $lastSession;
    public $usedCapacity;
    public $workshopTitle;
    public $email;
    public $mobile;

    public $filters = array('email', 'mobile', 'username', 'displayName', 'firstName', 'lastName', 'itemName', 'isNew', 'usedCapacity', 'firstSession', 'lastSession', 'workshopTitle',);

    public $isNew = true;

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
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
     * @param mixed $workshopTitle
     */
    public function setWorkshopTitle($workshopTitle)
    {
        $this->workshopTitle = $workshopTitle;
    }

    /**
     * @return mixed
     */
    public function getWorkshopTitle()
    {
        return $this->workshopTitle;
    }


    /**
     * @param int $workshopId
     */
    public function setWorkshopId($workshopId)
    {
        $this->workshopId = $workshopId;
    }

    /**
     * @return int
     */
    public function getWorkshopId()
    {
        return $this->workshopId;
    }

    /**
     * @param mixed $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param mixed $educatorId
     */
    public function setEducatorId($educatorId)
    {
        $this->educatorId = $educatorId;
    }

    /**
     * @return mixed
     */
    public function getEducatorId()
    {
        return $this->educatorId;
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
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
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
     * @param mixed $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $firstSession
     */
    public function setFirstSession($firstSession)
    {
        $this->firstSession = $firstSession;
    }

    /**
     * @return mixed
     */
    public function getFirstSession()
    {
        return $this->firstSession;
    }

    /**
     * @param mixed $lastSession
     */
    public function setLastSession($lastSession)
    {
        $this->lastSession = $lastSession;
    }

    /**
     * @return mixed
     */
    public function getLastSession()
    {
        return $this->lastSession;
    }

    /**
     * @param mixed $usedCapacity
     */
    public function setUsedCapacity($usedCapacity)
    {
        $this->usedCapacity = $usedCapacity;
    }

    /**
     * @return mixed
     */
    public function getUsedCapacity()
    {
        return $this->usedCapacity;
    }

}