<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Koushan
 * Date: 6/19/13
 * Time: 3:18 PM
 * To change this template use File | Settings | File Templates.
 */

namespace OnlineOrders\Model;


use System\Model\BaseModel;

class Language extends BaseModel
{

    public $id;
    public $langName;
    public $langCode;


    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLangCode($langCode)
    {
        $this->langCode = $langCode;
    }

    public function getLangCode()
    {
        return $this->langCode;
    }

    public function setLangName($langName)
    {
        $this->langName = $langName;
    }

    public function getLangName()
    {
        return $this->langName;
    }






}