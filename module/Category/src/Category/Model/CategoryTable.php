<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Category\Model;

use Zend\Db;
use Zend\Db\TableGateway;

class CategoryTable extends \System\DB\BaseTableGateway
{
    protected $table = 'tbl_category';
    protected $model = 'Category\Model\Category';
    protected $caches = array('all_category_array');

    public function getByMachineName($name)
    {
        $item = $this->select(array('catMachineName' => $name));
        if ($item->count()) {
            return $item->current();
        } else
            return false;
    }

    public function getAllArray()
    {
        $cache_key = 'all_category_array';
        if (!$items = getCache()->getItem($cache_key)) {
            $cat = $this->getAll();
            if ($cat) {
                foreach ($cat as $row)
                    $items[$row->id] = $row->catName;
                getCache()->setItem($cache_key, $items);
            }
        }
        return $items;
    }

    public function searchCategoryList($term)
    {
        $where['catName like ?'] = '%' . $term . '%';
        return $this->getAll($where, array('catName ASC'));
    }

    public function getCounts()
    {
        $dataArray['count'] = 0;
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(new \Zend\Db\Sql\Expression('COUNT(tbl_category.id) AS id')));
        $data = $this->selectWith($select)->current();
        if (isset($data->id))
            $dataArray['count'] = $data->id;
        return $dataArray;
    }
}
