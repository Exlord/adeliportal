<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Menu\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class MenuTable extends BaseTableGateway
{
    protected $table = 'tbl_menu';
    protected $model = 'Menu\Model\Menu';
    protected $caches = array('menus');
    protected $cache_prefix = null;


    public function getArray()
    {
        $cache_key = 'menus';
        if (!$menus = getCacheItem($cache_key)) {
            $data = $this->select();
            $menus = array();
            foreach ($data as $row) {
                $menus[$row->id] = $row->menuTitle;
            }
            setCacheItem($cache_key, $menus);
        }
        return $menus;
    }

    public function getByName($name)
    {
        return $this->select(array('menuName' => $name))->current();
    }

    public function getCounts()
    {
        $dataArray = array();
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(new \Zend\Db\Sql\Expression('COUNT(tbl_menu.id) AS id'), 'id'));
        $select->group($this->table . '.id');
        $data = $this->selectWith($select)->current();
        if ($data->id)
            $dataArray['count'] = $data->id;
        return $dataArray;
    }
}
