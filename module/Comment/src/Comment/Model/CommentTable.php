<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Comment\Model;

use System\DB\BaseTableGateway;
use Zend\Db\Sql\Expression;

class CommentTable extends BaseTableGateway
{
    protected $table = 'tbl_comment';
    protected $model = 'Comment\Model\Comment';
    protected $caches = null;
    protected $cache_prefix = array('comment_count_');

    /*public function getComments($count = 8, $entityId = null, $entityType = null, $parentId = null, $startOffset = 0, $id = 0)
    {
        $data = array();
        $where = array();

        $where = array(
            $this->table . '.entityType' => $entityType,
            $this->table . '.status' => 1,
        );
        if ($id)
            $where[$this->table . '.id'] = $id;
        if ($entityId)
            $where[$this->table . '.entityId'] = $entityId;
        elseif ($parentId)
            $where[$this->table . '.parentId'] = $parentId;

        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();

        $join = new \Zend\Db\Sql\Expression($this->table . '.id=c.parentId AND c.status=1');
        $select->join(array('c' => $this->table), $join,
            array('count' => new \Zend\Db\Sql\Expression('COUNT(c.id)')), 'LEFT');
        $select->join(array('u' => 'tbl_user_profile'), $this->table . '.userId=u.userId', array('image'));

        $select->where($where);
        $select->group($this->table . '.id');
        if ($entityId) {
            $select->limit($count);
            $select->offset(intval($startOffset));
        }
        $data = $this->selectWith($select)->toArray();

        $this->swapResultSetPrototype();
        return $data;
    }*/

    public function getCounts($entityId, $entityType)
    {
        $cache_key = 'comment_count_' . $entityType . '_' . $entityId;
        if (!$count = getCacheItem($cache_key)) {
            $this->swapResultSetPrototype();
            $select = $this->getSql()->select();
            $select->columns(array('count' => new Expression('COUNT(*)')))
                ->where(array(
                        'entityId' => $entityId,
                        'entityType' => $entityType,
                        'status' => 1,)
                );
            $result = $this->selectWith($select)->current();
            $count = $result['count'];
            $this->swapResultSetPrototype();
            setCacheItem($cache_key, $count);
        }
        return $count;
    }

    public function getEntityType()
    {
        $data = array();
        $select = $this->getSql()->select();
        $select->columns(array('entityType' => new Expression('DISTINCT(entityType)')));
        $result = $this->selectWith($select);
        foreach ($result as $row) {
            $data[$row->entityType] = t($row->entityType);
        }
        return $data;
    }

    public function getComments($count = 8, $parentId = 0, $entityType, $entityId, $id = null, $startOffset = null)
    {
        //region One level
        $this->swapResultSetPrototype();
        $where = array(
            $this->table . '.entityType' => $entityType,
            $this->table . '.entityId' => $entityId,
            $this->table . '.status' => 1,
        );
        $where[$this->table . '.parentId'] = $parentId;
        if ($id)
            $where[$this->table . '.id'] = $id;
        $sql = $this->getSql();
        $select = $sql->select();
        if ($count == -1) {
            $join = new Expression($this->table . '.id=c.parentId AND c.status=1');
            $select->join(array('c' => $this->table), $join,
                array('count' => new Expression('COUNT(c.id)')), 'LEFT');
        }
        $select->join(array('u' => 'tbl_user_profile'), $this->table . '.userId=u.userId', array('image'), 'LEFT');
        $select->where($where);
        if ($count > 0) // agar 0 bashad hame ra biavarad
        {
            $select->limit((int)$count);
            if ($startOffset)
                $select->offset(intval($startOffset));
        }

        if ($count == -1)
            $select->group($this->table . '.id');
        $dataOneLevel = $this->selectWith($select)->toArray();
        $this->swapResultSetPrototype();
        //endregion
        return $dataOneLevel;
    }

    /**
     * Get a count of all the comments that needs approval by admin
     *
     * @return int
     */
    public function getUnapprovedCount()
    {
        //TODO cache this
        $this->swapResultSetPrototype();
        $select = $this->getSql()->select();
        $select
            ->where(array('status' => 0))
            ->columns(array('count' => new Expression('COUNT(id)')));
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


