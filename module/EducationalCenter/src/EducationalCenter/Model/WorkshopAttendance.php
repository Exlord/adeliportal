<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */
namespace EducationalCenter\Model;

use System\Model\BaseModel;

class WorkshopAttendance extends BaseModel
{
    public $id;
    public $userId;
    public $classId;
    public $registerDate;
    public $paymentStatus = 0;
    public $paymentId = 0;
    public $status = 0;
    public $note = '';

    //region External Fields
    public $username;
    public $displayName;
    public $firstName;
    public $lastName;
    public $count = 0;
    public $workshopId;
    public $workshopTitle;
    public $classTitle;
    public $firstSession;
    public $mobile;
    public $email;
    public $price;
    //endregion

    public $filters = array('username', 'displayName', 'email', 'firstName', 'lastName', 'mobile', 'itemName', 'count', 'workshopId', 'workshopTitle', 'classTitle', 'firstSession', 'price');

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
     * @param mixed $classTitle
     */
    public function setClassTitle($classTitle)
    {
        $this->classTitle = $classTitle;
    }

    /**
     * @return mixed
     */
    public function getClassTitle()
    {
        return $this->classTitle;
    }

    /**
     * @param mixed $workshopId
     */
    public function setWorkshopId($workshopId)
    {
        $this->workshopId = $workshopId;
    }

    /**
     * @return mixed
     */
    public function getWorkshopId()
    {
        return $this->workshopId;
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
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
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
     * @param mixed $classId
     */
    public function setClassId($classId)
    {
        $this->classId = $classId;
    }

    /**
     * @return mixed
     */
    public function getClassId()
    {
        return $this->classId;
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
     * @param mixed $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param mixed $paymentStatus
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * @return mixed
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @param mixed $registerDate
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
    }

    /**
     * @return mixed
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
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
