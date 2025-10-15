<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */

/*`id` int(11) NOT NULL AUTO_INCREMENT,
  `fName` varchar(400) DEFAULT NULL,
`fTitle` varchar(400) DEFAULT NULL,
`fAlt` varchar(400) DEFAULT NULL,
  `fPath` varchar(400) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `entityType` varchar(200) DEFAULT NULL,
  `entityId` int(11) DEFAULT NULL,*/

namespace File\Model;
class File
{
    public $id;
    public $fName;
    public $fPath;
    public $status=1;
    public $entityType;
    public $entityId;
    public $fAlt;
    public $fTitle;
    public $fileType;

    /**
     * @param mixed $fileType
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
    }

    /**
     * @return mixed
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    public function setFTitle($fTitle)
    {
        $this->fTitle = $fTitle;
    }

    public function getFTitle()
    {
        return $this->fTitle;
    }

    public function setFAlt($fAlt)
    {
        $this->fAlt = $fAlt;
    }

    public function getFAlt()
    {
        return $this->fAlt;
    }

    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    public function getEntityId()
    {
        return $this->entityId;
    }

    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    }

    public function getEntityType()
    {
        return $this->entityType;
    }

    public function setFName($fName)
    {
        $this->fName = $fName;
    }

    public function getFName()
    {
        return $this->fName;
    }

    public function setFPath($fPath)
    {
        $this->fPath = $fPath;
    }

    public function getFPath()
    {
        return $this->fPath;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
