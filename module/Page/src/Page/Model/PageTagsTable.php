<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Page\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class PageTagsTable extends BaseTableGateway
{
    protected $table = 'tbl_tags_page';
    protected $model = 'Page\Model\PageTags';
    protected $cache_prefix = null;
    protected $primaryKey = 'pageId';

    public function getTags($pageId)
    {
        $rsp = $this->getResultSetPrototype();
        $this->resultSetPrototype = new Db\ResultSet\HydratingResultSet();
        $where = array(
            $this->table . '.pageId' => $pageId,
        );
        /* if ($pageId)
             $where['t.pageId'] = $pageId;*/

        $sql = $this->getSql();
        $select = $sql->select();
        $select->where($where);
        $select->join(array('c' => 'tbl_category_item'), $this->table . '.tagsId=c.id');

        $select->order(array('id DESC'));
        $data = $this->selectWith($select);
        $this->resultSetPrototype = $rsp;
        return $data;
    }

    public function getMaxPageId($tagId)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->where(array(
            'tagsId' => $tagId,
        ));
        $select->columns(array(new \Zend\Db\Sql\Expression('MAX(pageId) as pageId')));
        $data = $this->selectWith($select)->current();
        if ($data)
            return $data->pageId;
        else
            return 0;
    }

    public function getPageCount()
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();

        $select->columns(array(new Db\Sql\Expression('COUNT(' . $this->table . '.pageId) as pageCount')));
        $catItem = new Db\Sql\Expression($this->table . '.tagsId=ci.id AND ci.itemStatus=1');
        $select->join(array('ci' => 'tbl_category_item'), $catItem, array('*'), \Zend\Db\Sql\Select::JOIN_LEFT);
        $page = new Db\Sql\Expression($this->table . '.pageId=p.id AND p.status=1');
        $select->join(array('p' => 'tbl_page'), $page, array());
        $select->group($this->table . '.tagsId');
        $select->order('ci.parentId DESC');
        $rawData = $this->selectWith($select);

        $counts = array();
        foreach ($rawData as $row) {
            if (!isset($counts[$row['id']]))
                $counts[$row['id']] = 0;

            if ((int)$row['parentId'] != 0) {
                if (!isset($counts[$row['parentId']]))
                    $counts[$row['parentId']] = 0;

                $counts[$row['parentId']] += (int)$row['pageCount'] + $counts[$row['id']];
            }

            $counts[$row['id']] += (int)$row['pageCount'];

        }
        $this->swapResultSetPrototype();
        return $counts;
    }

    public function removeByPageId($id)
    {
        $this->delete(array('pageId' => $id));
    }
}
