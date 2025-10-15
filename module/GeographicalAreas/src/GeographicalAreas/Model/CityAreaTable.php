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

class CityAreaTable extends BaseTableGateway
{
    protected $table = 'tbl_city_area_list';
    protected $model = 'GeographicalAreas\Model\CityArea';
    protected $caches = '';
    protected $cache_prefix = array('area_list_', 'sub_area_list_');

    public function getArray($cityId = 0, $parentId = -2) //parentId = null : not || parentId > 0 : haman parent id ra bar migardanad || parentId = -1 : parent id haye >0 ra bar migardanad
    {
        $cache_key = 'area_list_' . $cityId;
        if (!$items = getCache()->getItem($cache_key)) {

            $where = array('itemStatus' => 1);
            if ($cityId)
                $where['cityId'] = $cityId;
            if ($parentId != -2 && $parentId != -1)
                $where['parentId'] = $parentId;
            if ($parentId == -1)
                $where['parentId > ?'] = $parentId;

            $items = array();
            $data = $this->getAll($where, array('itemOrder DESC', 'areaTitle ASC'));
            // $items[0] = t('-- Select --');
            foreach ($data as $row) {
                $items[$row->id] = $row->areaTitle;
            }

            //    getCache()->setItem($cache_key, $items);
        }
        return $items;
    }

    public function getSubArray($parentId = 0)
    {
        $cache_key = 'sub_area_list_' . $parentId;
        if (!$items = getCache()->getItem($cache_key)) {

            $where = array('itemStatus' => 1);

            $where['parentId'] = $parentId;
            $items = array();
            $data = $this->getAll($where, array('itemOrder DESC', 'areaTitle ASC'));
            /*$items[0] = t('-- Select --');*/
            foreach ($data as $row) {
                $items[$row->id] = $row->areaTitle;
            }

            // getCache()->setItem($cache_key, $items);
        }
        return $items;
    }

    public function getAreaName($areaId)
    {
        if ($areaId) {
            $select = $this->get($areaId);
            if ($select)
                if (isset($select->areaTitle))
                    return $select->areaTitle;

        }
        return '';
    }

    public function removeByParentId($parentId)
    {
        $this->delete(array('parentId'=>$parentId));
    }

}
