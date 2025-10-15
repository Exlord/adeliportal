<?php
namespace Gallery\Model;

use System\DB\BaseTableGateway;

class OrderBannerTable extends BaseTableGateway
{
    protected $table = 'tbl_order_banner';
    protected $model = 'Gallery\Model\OrderBanner';
    protected $caches = array('orderBanner_counts');
    protected $cache_prefix = null;

    public function getCounts()
    {
        $data = array();
        $cache_key = 'orderBanner_counts';

        if (!$data = getCacheItem($cache_key)) {
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
