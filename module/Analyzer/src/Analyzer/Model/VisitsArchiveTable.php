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
use Zend\Db\TableGateway;
use Zend\Db\Sql\Expression;

class VisitsArchiveTable extends BaseTableGateway
{
    protected $table = 'tbl_visits_archive';
    protected $model = 'Analyzer\Model\VisitsArchive';
    protected $caches = array('all_visits_counts');
    protected $cache_prefix = null;

    public function getCount($dateLow, $dateHigh, $unique = true)
    {
        $this->swapResultSetPrototype();
        $where = array();
        if ($dateLow)
            $where['date >= ?'] = $dateLow;
        if ($dateHigh)
            $where['date < ?'] = $dateHigh;

        $select = $this->getSql()->select();
        $select
            ->columns(array(
                'count' => new Expression('SUM(count)'),
                'uniqueCount' => new Expression('SUM(uniqueCount)')
            ))
            ->where($where);

        $data = $this->selectWith($select);
        $this->swapResultSetPrototype();
        if ($data) {
            $data = $data->current();
            if ($unique)
                $count = (int)$data['uniqueCount'];
            else
                $count = (int)$data['count'] + (int)$data['uniqueCount'];
            return $count;
        }
        return 0;
    }

    public function archive($date, $count, $uniqueCount)
    {
        $countField = $this->getAdapter()->getPlatform()->quoteIdentifier('count');
        $uniqueCountField = $this->getAdapter()->getPlatform()->quoteIdentifier('uniqueCount');
        $q = "INSERT INTO %s (%s,%s,%s) VALUES (?,?,?) ON DUPLICATE KEY UPDATE %s=%s+%s , %s=%s+%s";
        $q = sprintf($q,
            $this->getAdapter()->getPlatform()->quoteIdentifier($this->table),
            $this->getAdapter()->getPlatform()->quoteIdentifier('date'),
            $countField,
            $uniqueCountField,
            $countField, $countField, $count,
            $uniqueCountField, $uniqueCountField, $uniqueCount
        );

        $this->getAdapter()->query($q, array(
            $date, $count, $uniqueCount,
        ));
    }

    public function getData($dateLow, $dateHigh)
    {
        return $this->select(array('date >= ?' => $dateLow, 'date < ?' => $dateHigh));
    }
}
