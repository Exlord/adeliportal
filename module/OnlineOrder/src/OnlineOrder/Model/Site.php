<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Koushan
 * Date: 6/19/13
 * Time: 3:18 PM
 * To change this template use File | Settings | File Templates.
 */

namespace OnlineOrder\Model;


use System\Model\BaseModel;

class Site extends BaseModel
{

   public $id;
   public $domainName;
   public $domainAlias;

    public function setDomainAlias($domainAlias)
    {
        $this->domainAlias = $domainAlias;
    }

    public function getDomainAlias()
    {
        return $this->domainAlias;
    }

    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
    }

    public function getDomainName()
    {
        return $this->domainName;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }




}