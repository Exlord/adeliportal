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


class StateTable extends BaseTableGateway
{
    protected $table = 'tbl_state_list';
    protected $model = 'GeographicalAreas\Model\State';
    protected $caches = null;
    protected $cache_prefix = array('states_list_', 'grouped_states_list');

    public function getArray($countryId = 0)
    {
        $cache_key = 'states_list_' . $countryId;
        if (!$items = getCache()->getItem($cache_key))
           {
            $where = array('itemStatus' => 1);
            if ($countryId)
                $where['countryId'] = $countryId;

            $items = array();
            $data = $this->getAll($where, array('itemOrder DESC', 'stateTitle ASC'));
            foreach ($data as $row) {
                $items[$row->id] = $row->stateTitle;
            }

            getCache()->setItem($cache_key, $items);
        }
        return $items;
    }

    public function getGroupedArray()
    {
        $cache_key = 'grouped_states_list';
        if (!$items = getCache()->getItem($cache_key))
           {
            $select = $this->getSql()->select();
            $select
                ->join(array('c' => 'tbl_country_list'), $this->table . '.countryId=c.id', array('countryTitle'))
                ->order(array('itemOrder DESC', 'stateTitle ASC'));
            $data = $this->selectWith($select);

            $items = array();
            foreach ($data as $row) {
                $items{$row->countryTitle}[$row->id] = $row->stateTitle;
            }

            getCache()->setItem($cache_key, $items);
        }
        return $items;
    }

    public function getCountryIds()
    {
        $cache_key = 'states_country_ids';
        if (!$items = getCache()->getItem($cache_key))
          {
            $data = $this->getAll(null, array('stateTitle ASC'));
            $items = array();
            foreach ($data as $row) {
                $items[$row->id] = $row->countryId;
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
