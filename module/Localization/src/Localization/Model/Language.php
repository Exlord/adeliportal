<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */

/*
      `id` int(11) NOT NULL AUTO_INCREMENT,
  `langSign` varchar(20) DEFAULT NULL,
  `langName` varchar(100) DEFAULT NULL,
  `langFlag` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
*/
namespace Localization\Model;
class Language
{
    public $id;
    public $langSign;
    public $langName;
    public $langFlag;
    public $status;
    public $default;

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLangFlag($langFlag)
    {
        $this->langFlag = $langFlag;
    }

    public function getLangFlag()
    {
        return $this->langFlag;
    }

    public function setLangName($langName)
    {
        $this->langName = $langName;
    }

    public function getLangName()
    {
        return $this->langName;
    }

    public function setLangSign($langSign)
    {
        $this->langSign = $langSign;
    }

    public function getLangSign()
    {
        return $this->langSign;
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
