<?php

namespace OnlineOrder\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class SiteTable extends BaseTableGateway
{
    protected $table = 'tbl_online_order_site';
    protected $model = 'OnlineOrder\Model\Site';
    protected $caches = null;
    protected $cache_prefix = null;

    /*public function getAliasDomain($domains)
    {
        $select = $this->getSql()->select();
        $select->where(array(
                    'domainName' => $domains
                )
            );
        $result = $this->selectWith($select)->toArray();
        return $result;
    }*/

    public function updateDomains($beforeDomains,$newDomains)
    {
        foreach($beforeDomains as $key=>$val)
        {
            $select = $this->getAll(array('domainName'=>$val))->current();
            $this->update(array('domainName'=>$newDomains[$key]),array('id'=>$select->id));
        }
    }
}


