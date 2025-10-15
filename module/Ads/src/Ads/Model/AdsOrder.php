<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */
namespace Ads\Model;

use System\Model\BaseModel;

class AdsOrder extends BaseModel
{
    public $baseType;
    public $secondType;
    public $order;
    public $homePage;

    /**
     * @param mixed $baseType
     */
    public function setBaseType($baseType)
    {
        $this->baseType = $baseType;
    }

    /**
     * @return mixed
     */
    public function getBaseType()
    {
        return $this->baseType;
    }

    /**
     * @param mixed $homePage
     */
    public function setHomePage($homePage)
    {
        $this->homePage = $homePage;
    }

    /**
     * @return mixed
     */
    public function getHomePage()
    {
        return $this->homePage;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $secondType
     */
    public function setSecondType($secondType)
    {
        $this->secondType = $secondType;
    }

    /**
     * @return mixed
     */
    public function getSecondType()
    {
        return $this->secondType;
    }

}
