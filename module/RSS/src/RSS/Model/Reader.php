<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */
namespace RSS\Model;

use System\Model\BaseModel;

class Reader extends BaseModel
{
    public $id;
    public $url;
    public $lastRead = 0;
    public $readInterval = 7;
    public $feedLimit = 10;
    public $title;
    public $status = 1;

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
     * @param mixed $feedLimit
     */
    public function setFeedLimit($feedLimit)
    {
        $this->feedLimit = $feedLimit;
    }

    /**
     * @return mixed
     */
    public function getFeedLimit()
    {
        return $this->feedLimit;
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
     * @param mixed $lastRead
     */
    public function setLastRead($lastRead)
    {
        $this->lastRead = $lastRead;
    }

    /**
     * @return mixed
     */
    public function getLastRead()
    {
        return $this->lastRead;
    }

    /**
     * @param mixed $readInterval
     */
    public function setReadInterval($readInterval)
    {
        $this->readInterval = $readInterval;
    }

    /**
     * @return mixed
     */
    public function getReadInterval()
    {
        return $this->readInterval;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }


}
