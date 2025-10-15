<?php

namespace OnlineOrder\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class CustomerTable extends BaseTableGateway
{
    protected $table = 'tbl_online_order_customer';
    protected $model = 'OnlineOrder\Model\Customer';
    protected $caches = array('onlineOrder_counts');
    protected $cache_prefix = null;

    public function getCounts()
    {
        $data = array();
        $cache_key = 'onlineOrder_counts';

        if ($data = getCacheItem($cache_key)) {
            $select = $this->getSql()->select();
            $select->columns(array('id' => new \Zend\Db\Sql\Expression('COUNT(id)')));
            $select->where(array('status' => 0));
            $row = $this->selectWith($select)->current();
            $data = array('count' => $row->id);
            setCacheItem($cache_key, $data);
        }

        return $data;
    }

}


