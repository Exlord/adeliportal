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

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql;

class CityTable extends BaseTableGateway
{
    protected $table = 'tbl_city_list';
    protected $model = 'GeographicalAreas\Model\City';
    protected $caches = array('cities_list');
    protected $cache_prefix = array('cities_list_for_country_', 'cities_list_');

    public function getAll($stateId = 0, $countryId = 0, $pageNumber = false)
    {
        if ($stateId)
            $cache_key = 'cities_list_for_state_' . $stateId;
        elseif ($countryId && !$stateId) {
            $cache_key = 'cities_list_for_country_' . $countryId;
        } else
            $cache_key = 'cities_list';

        if (!$items = getCache()->getItem($cache_key))
           {
            $where = null;
            if ($stateId) {
                $where['tbl_city_list.stateId'] = $stateId;
            }
            if ($countryId && !$stateId)
                $where['s.countryId'] = $countryId;

            $sql = $this->sql;
            $select = $sql->select()
                ->columns(array('*',))
                ->join(array('s' => 'tbl_state_list'), $this->table . '.stateId = s.id', array('stateTitle', 'stateStatus' => 'itemStatus', 'countryId',))
                ->join(array('co' => 'tbl_country_list'), 's.countryId = co.id', array('countryTitle', 'countryStatus' => 'itemStatus',))
                ->where($where)
                ->order(array('itemOrder DESC', 'cityTitle ASC'));

            if ($pageNumber) {
                $adapter = new DbSelect($select, $sql);
                $pagination = new Paginator($adapter);
                $pagination->setCurrentPageNumber($pageNumber);
//            $pagination->setItemCountPerPage(2);
                $items = $pagination;
            } else {
                $items = $this->selectWith($select);
            }

//            getCache()->setItem($cache_key, $items);
        }
        return $items;
    }

    public function getArray($stateId = 0)
    {
        $cache_key = 'cities_list_' . $stateId;
        if (!$items = getCache()->getItem($cache_key)) {

            $where = array('itemStatus' => 1);
            if ($stateId)
                $where['stateId'] = $stateId;

            $items = array();
            $data = parent::getAll($where, array('itemOrder DESC', 'cityTitle ASC'));
            foreach ($data as $row) {
                $items[$row->id] = $row->cityTitle;
            }

            getCache()->setItem($cache_key, $items);
        }
        return $items;
    }

    public function getAllActive(){
        return $this->getAll(array('itemStatus' => 1));
    }
}
