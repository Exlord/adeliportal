<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Analyzer\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway;

class VisitsTable extends BaseTableGateway
{
    protected $table = 'tbl_visits';
    protected $model = 'Analyzer\Model\Visits';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getCount($unique = true)
    {
        $today = strtotime("00:00:00");
        $where = array('date >= ?' => $today);
        if ($unique)
            $where['type'] = '1';

        $this->swapResultSetPrototype();
        $select = $this->getSql()->select();
        $select
            ->columns(array('count' => new Expression('COUNT(*)')))
            ->where($where);

        $data = $this->selectWith($select);
        $this->swapResultSetPrototype();
        if ($data) {
            $data = $data->current();
            return (int)$data['count'];
        }
        return 0;
    }
}
