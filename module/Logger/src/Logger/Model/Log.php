<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 1:07 PM
 */

/*
CREATE TABLE `tbl_event_loges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` tinyint(1) DEFAULT '6',
  `message` text,
  `uid` int(11) DEFAULT NULL,
`timestamp` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */
namespace Logger\Model;
class Log
{
    public $id;
    public $priority;
    public $message;
    public $uid;
    public $timestamp;
    public $entityType;

    /**
     * @param mixed $entityType
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    public function getUid()
    {
        return $this->uid;
    }
}
