<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */
namespace Mail\Model;

use System\Model\BaseModel;

class Send extends BaseModel
{
    public $id;
    public $sendTime;
    public $sendCount;

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
     * @param mixed $sendCount
     */
    public function setSendCount($sendCount)
    {
        $this->sendCount = $sendCount;
    }

    /**
     * @return mixed
     */
    public function getSendCount()
    {
        return $this->sendCount;
    }

    /**
     * @param mixed $sendTime
     */
    public function setSendTime($sendTime)
    {
        $this->sendTime = $sendTime;
    }

    /**
     * @return mixed
     */
    public function getSendTime()
    {
        return $this->sendTime;
    }


}
