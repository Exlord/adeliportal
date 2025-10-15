<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Gallery\Model;

use System\DB\BaseTableGateway;
use Zend\Db\Sql\Select;

class GalleryItemTable extends BaseTableGateway
{
    protected $table = 'tbl_gallery_item';
    protected $model = 'Gallery\Model\GalleryItem';
    protected $caches = array('gallery_page_list');
    protected $cache_prefix = null;

    public function getItems($Id)
    {
        $data = $this->getAll(array('status' => 1, 'groupId' => $Id), array('title ASC'));
        if ($data->count()) {
            $data = $this->toNestedArray($data);
            return $data;
        }
        return array();
    }

    public function randomItem($groupId, $type)
    {
        $rand = new \Zend\Db\Sql\Expression('RAND()');
        $select = $this->getAll(
            array(
                'groupId' => $groupId,
                'type' => $type,
                'status' => 1
            ), array($rand), 1)->current();
        return $select;
    }

    public function getOrderItem($groupId, $type, $id = 0, $maxId = -1)
    {
        if ($maxId != -1)
            if ($id == $maxId)
                $id = 0;

        $selectItem = $this->getAll(
            array(
                'groupId' => $groupId,
                'type' => $type,
                'status' => 1,
                'id > ?' => $id,
            ), 'id', 1)->current();

        if ($maxId == -1) {
            $sql = $this->getSql();
            $select = $sql->select();
            $select->where(array(
                'groupId' => $groupId,
                'type' => $type,
                'status' => 1,
            ));
            $select->columns(array(new \Zend\Db\Sql\Expression('MAX(id) as id')));
            $data = $this->selectWith($select)->current();
            $selectItem->maxId = $data->id;
        }

        return $selectItem;
    }

    public function updateBannerItemHit($id, $hitsType)
    {
        $hitsName = 'hits';
        if ($hitsType == 'app')
            $hitsName = 'appHits';
        $this->update(array(
            $hitsName => new \Zend\Db\Sql\Expression($hitsName . ' + 1')
        ), array('id' => $id));
        return $this->get($id);
    }

    public function getGroupIdRandom($groupIds = array())
    {
        if ($groupIds && count($groupIds)) {
//            $rand = new \Zend\Db\Sql\Expression('RAND()');
//            $rand = 'id ASC';

            $platform = $this->getAdapter()->getPlatform();
            $table = $platform->quoteIdentifier($this->table);
            $groupId = $platform->quoteIdentifier('groupId');
            $type = $platform->quoteIdentifier('type');
            $status = $platform->quoteIdentifier('status');

            $q = null;
            foreach ($groupIds as $row) {
                $select = sprintf("(SELECT * from %s WHERE %s=%s AND %s='banner' AND %s=1 ORDER BY RAND() LIMIT 1)",
                    $table, $groupId, $row['groupId'], $type, $status
                );
                if (!$q)
                    $q = $select;
                else
                    $q .= 'UNION ' . $select;
//                $newSelect = new Select($this->table);
//                $newSelect->where(array(
//                    'groupId' => (int)$groupId[0]['groupId'],
//                    'type' => 'banner',
//                    'status' => 1
//                ))
//                    ->limit(1)
//                    ->order(array($rand));


//                if ($select != null)
//                    $select->combine($newSelect);
//                else
//                    $select = $newSelect;
//                $newSelect = null;
            }
            /* echo $select->getSqlString();
             die;*/
            $result = $this->getAdapter()->query($q)->execute();
            return $result;
        }
        return '';
    }

    public function getAllGalleryItems()
    {
        $cache_key = 'gallery_page_list';
        if (!$data = getCacheItem($cache_key)) {
            $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
            $sql = $this->getSql();
            $select = $sql->select();
            $select->where(array($this->table . '.status' => 1, $this->table . '.type' => 'gallery','g.showType'=>1));
            $select->join(array('g' => 'tbl_gallery'), $this->table . '.groupId=g.id', array('groupName'));
            $select->order(array($this->table . '.groupId DESC'));
            $select = $this->selectWith($select);
            foreach ($select as $row)
                $data[$row->groupId][] = $row;
            setCacheItem($cache_key, $data);
        }
        return $data;
    }

}
