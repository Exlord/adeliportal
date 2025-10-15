<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Rating\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class RatingTable extends BaseTableGateway
{
    const RATE_AVG = 'avg';
    const RATE_SUM = 'sum';

    protected $table = 'tbl_rating';
    protected $model = 'Rating\Model\Rating';
    protected $caches = null;
    protected $cache_prefix = null;

    private function getRate($entityId, $entityType, $type, $entityId2 = null)
    {
        $where = array(
            $this->table . '.entityId' => $entityId,
            $this->table . '.entityType' => $entityType,
        );

        if ($entityId2)
            $where[$this->table . '.entityId2'] = $entityId2;

        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();
        $select->where($where);
        if ($type === self::RATE_AVG)
            $select->columns(array(new Db\Sql\Expression('AVG(rateScore) as rateScore')));
        elseif ($type === self::RATE_SUM) {
            $select->columns(array(new Db\Sql\Expression('SUM(' . $this->table . '.rateScore) as rateScore')));
            $rateScore = new Db\Sql\Expression($this->table . '.userId=r.userId AND r.entityId="' . $entityId . '" AND r.entityType="' . $entityType . '"'); // TODO fix Security Problems
            $select->join(array('r' => 'tbl_rating'), $rateScore, array('userRate' => 'rateScore'), 'left');
        }

        $data = $this->selectWith($select)->current();
        $this->swapResultSetPrototype();
        return $data;
    }

    public function getAverageRate($entityId, $entityType, $entityId2 = null)
    {
        return $this->getRate($entityId, $entityType, self::RATE_AVG, $entityId2);
    }

    public function getSumRate($entityId, $entityType)
    {
        return $this->getRate($entityId, $entityType, self::RATE_SUM);
    }

    public function getVote($entityId, $entityType, $entityId2 = null)
    {
        $where = array(
            'entityId' => $entityId,
            'entityType' => $entityType,
        );
        if ($entityId2)
            $where['entityId2'] = $entityId2;

        $select = $this->getAll($where)->current();
        if ($select)
            return $select->rateScore;
        else
            return 0;
    }

    public function removeRate($entityId, $entityType)
    {
        $select = $this->getAll(array('entityId' => $entityId, 'entityType' => $entityType));
        foreach ($select as $row)
            $this->remove($row->id);
    }

    public function rate($entityId, $entityType, $userId, $vote, $entityId2 = null)
    {
        $data = array(
            'entityId' => $entityId,
            'entityType' => $entityType,
            'userId' => $userId,
        );
        if ($entityId2)
            $data['entityId2'] = $entityId2;

        $result = $this->select($data);
        if ($result && $result = $result->current()) {
            $data['id'] = $result->id;
        }

        $data['rateScore'] = $vote;
        $data['date'] = time();

        $this->save($data);
    }

}


