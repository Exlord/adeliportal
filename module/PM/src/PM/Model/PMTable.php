<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace PM\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class PMTable extends BaseTableGateway
{
    protected $table = 'tbl_pm';
    protected $model = 'PM\Model\PM';
    protected $caches = null;
    protected $cache_prefix = null;

    public function get($id)
    {
        $select = $this->getSql()->select();
        $select
            ->join(array('u' => 'tbl_users'), $this->table . '.from=u.id', array('username'))
            ->where(array($this->table . '.id' => $id));

        return $this->selectWith($select);
    }

    public function save($model)
    {
        if (is_array($model->params))
            $model->params = serialize($model->params);
        return parent::save($model);
    }

    public function getUnreadCount()
    {
        //TODO cache this
        $this->swapResultSetPrototype();
        $select = $this->getSql()->select();
        $select
            ->where(array('status' => 0, 'to' => current_user()->id))
            ->columns(array('count' => new Db\Sql\Expression('COUNT(id)')));
        $result = $this->selectWith($select);
        $count = 0;
        if ($result) {
            $count = $result->current();
            $count = $count['count'];
        }
        $this->swapResultSetPrototype();
        return $count;
    }
}
