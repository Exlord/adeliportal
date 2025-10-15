<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Category\Model;

use Zend\Db;
use Zend\Db\TableGateway;

class EntityRelationTable extends \System\DB\BaseTableGateway
{
    protected $table = 'tbl_category_item_entity';
    protected $model = 'Category\Model\EntityRelation';

    /**
     * @param int|string|array $itemId
     */
    public function removeByItem($itemId)
    {
        if (!is_array($itemId))
            $itemId = array($itemId);
        $this->delete(array('itemId' => $itemId));
    }

    /**
     * @param string|array $type
     */
    public function removeByEntityType($type)
    {
        if (!is_array($type))
            $type = array($type);
        $this->delete(array('entityType' => $type));
    }

    /**
     * @param int|string $id
     * @param string $type
     */
    public function removeByEntityId($id, $type)
    {
        $this->delete(array('entityId' => $id, 'entityType' => $type));
    }

    /**
     * @param int|string $entityId
     * @param string $entityType
     * @return Db\ResultSet\ResultSet
     */
    public function getItemsId($entityId, $entityType)
    {
        return $this->select(array('entityId' => $entityId, 'entityType' => $entityType));
    }

    public function getItemsIdArray($entityId, $entityType)
    {
        $dataArray = array();
        $select = $this->getItemsId($entityId, $entityType);
        if ($select)
            foreach ($select as $row)
                $dataArray[] = $row->itemId;
        return $dataArray;
    }

    public function getItemsNameArray($entityId, $entityType)
    {
        $dataArray = array();
        $select = $this->getItems($entityId, $entityType);
        if ($select)
            foreach ($select as $row)
                $dataArray[$row->itemId] = $row->itemName;
        return $dataArray;
    }

    /**
     * @param int|string $entityId
     * @param string $entityType
     * @return array|\ArrayObject|int|null
     */
    public function getSingleItemId($entityId, $entityType)
    {
        $result = $this->getItemsId($entityId, $entityType);
        if ($result)
            return $result->current();
        else
            return 0;
    }

    /**
     * @param int|string $entityId
     * @param string $entityType
     * @return Db\ResultSet\HydratingResultSet
     */
    public function getItems($entityId, $entityType, $limit = false)
    {
        $protoType = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $select = $this->getSql()->select()
            ->join(array('ci' => 'tbl_category_item'), $this->table . '.itemId=ci.id',array('itemName'=>'itemName','itemId'=>'id'))
            ->where(array('entityId' => $entityId, 'entityType' => $entityType));
        if ($limit)
            $select->limit($limit);
        $select = $this->selectWith($select);
        $this->resultSetPrototype = $protoType;
        return $select;
    }

    /**
     * @param int|string $entityId
     * @param string $entityType
     * @return null|object
     */
    public function getSingleItem($entityId, $entityType)
    {
        $result = $this->getItems($entityId, $entityType, $limit = 1);
        if ($result)
            return $result->current();
        else
            return null;
    }

    public function saveAll($entityId, $entityType, $catItems)
    {
        $this->removeByEntityId($entityId,$entityType);
        $catItem = array();
        foreach ($catItems as $val) {
            $item = array(
                'entityId' => $entityId,
                'entityType' => $entityType,
                'itemId' => $val,
            );
            $catItem[] = $item;
        }
        $this->multiSave($catItem);
    }

}
