<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace GeographicalAreas\Model;

use System\DB\BaseTableGateway;
use Zend\Db;

use Zend\Db\TableGateway;

class CountryTable extends BaseTableGateway
{
    protected $table = 'tbl_country_list';
    protected $model = 'GeographicalAreas\Model\Country';
    protected $caches = array('countries_list_array');
    protected $cache_prefix = null;

    public function getArray()
    {
        $cache_key = 'countries_list_array';
        if (!$items = getCache()->getItem($cache_key))
           {
            $where = array('itemStatus' => 1);
            $data = $this->getAll($where, array('countryTitle ASC'));
            $items = array();
            foreach ($data as $row) {
                $items[$row->id] = $row->countryTitle;
            }

            getCache()->setItem($cache_key, $items);
        }
        return $items;
    }

    public function getAllActive()
    {
        return $this->getAll(array('itemStatus' => 1));
    }
}
