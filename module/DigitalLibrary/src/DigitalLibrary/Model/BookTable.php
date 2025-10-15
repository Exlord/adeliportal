<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace DigitalLibrary\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class BookTable extends BaseTableGateway
{
    protected $table = 'tbl_dl_book';
    protected $model = 'DigitalLibrary\Model\Book';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getItemsList($fieldsTable, $page = 1, $perPage = 20, $where = null)
    {
        $this->swapResultSetPrototype();
        $select = $this->getSql()->select();
        $select
            ->columns(array('itemId' => 'id', 'title'))
            ->join(array('f' => $fieldsTable), $this->table . '.id=f.entityId', array('*'))
            ->order('id DESC');
        if ($where)
            $select->where($where);

        $result = $this->getPaginated($select, $this->getSql(), $page, $perPage);
        $this->swapResultSetPrototype();
        return $result;
    }

    public function getData($id, $fieldsTable)
    {
        $this->swapResultSetPrototype();
        $select = $this->getSql()->select();
        $select
            ->columns(array('itemId' => 'id', 'title'))
            ->join(array('f' => $fieldsTable), $this->table . '.id=f.entityId', array('*'))
            ->where(array($this->table . '.id' => $id));

        $result = $this->selectWith($select);
        $this->swapResultSetPrototype();
        if ($result) {
            $result = $result->current();
            if ($result)
                return $result;
        } else
            return null;
    }
}
