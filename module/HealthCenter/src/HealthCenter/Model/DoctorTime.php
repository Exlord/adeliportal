<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/31/14
 * Time: 10:57 AM
 */

namespace HealthCenter\Model;


use System\Model\BaseModel;

class DoctorTime extends BaseModel
{
    public $id;
    public $doctorId;
    public $date;
    public $start;
    public $end;
    public $status = 0;
    public $resStatus;
    public $userId;
    public $resId = 0;

    public $filters = array('resStatus', 'userId','resId');

    /**
     * @param int $resId
     */
    public function setResId($resId)
    {
        $this->resId = $resId;
    }

    /**
     * @return int
     */
    public function getResId()
    {
        return $this->resId;
    }


    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $doctorId
     */
    public function setDoctorId($doctorId)
    {
        $this->doctorId = $doctorId;
    }

    /**
     * @return mixed
     */
    public function getDoctorId()
    {
        return $this->doctorId;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
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
     * @param mixed $resStatus
     */
    public function setResStatus($resStatus)
    {
        $this->resStatus = $resStatus;
    }

    /**
     * @return mixed
     */
    public function getResStatus()
    {
        return $this->resStatus;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
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