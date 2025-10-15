<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace RSS\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class ReaderTable extends BaseTableGateway
{
    protected $table = 'tbl_rss_reader';
    protected $model = 'RSS\Model\Reader';
    protected $caches = array('rss_reader_feed_list');
    protected $cache_prefix = null;

    public static $readInterval = array(
        1 => '5 minutes',
        2 => '15 minutes',
        3 => '30 minutes',
        4 => '1 hour',
        5 => '6 hours',
        6 => '12 hours',
        7 => '1 day',
        8 => '3 days',
        9 => '1 week',
        10 => '2 weeks',
        11 => '1 month',
    );

    public function getArray()
    {
        $cacheKey = 'rss_reader_feed_list';
        if (!$list = getCacheItem($cacheKey))
            {
            $data = $this->getAll(array('status' => 1));
            $list = array();
            foreach ($data as $row) {
                $list[$row->id] = $row->title;
            }
            setCacheItem($cacheKey, $list);
        }
        return $list;
    }

}
