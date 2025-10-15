<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace EducationalCenter\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;
use Zend\Stdlib\ArrayObject;

class WorkshopTable extends BaseTableGateway
{
    protected $table = 'tbl_ec_workshop';
    protected $model = 'EducationalCenter\Model\Workshop';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getItem($id)
    {
        return parent::get($id);
    }

    public function get($id)
    {
        if (!is_array($id))
            $id = array($id);

        array_walk($id, function (&$item, $index) {
            $item = (int)$item;
        });

        $select = $this->getSql()->select();
        $select
            ->join(array('ci' => 'tbl_category_item'), $this->table . '.catId=ci.id', array('itemName'), 'LEFT')
            ->where(array($this->table . '.id' => $id));
        $result = $this->selectWith($select);
        return $result;
    }

    public function getAvailable($page = 1, $perPage = 10)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->join(array('ci' => 'tbl_category_item'), $this->table . '.catId=ci.id', array('itemName'), 'LEFT')
            ->where(array($this->table . '.status' => 1));
        return $this->getPaginated($select, $sql, $page, $perPage);
    }

    public function remove($id)
    {
        getSM('ec_workshop_class_table')->removeByWorkshop($id);
        parent::remove($id);
    }
}
