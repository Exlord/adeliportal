<?php
namespace OrgChart\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class ChartNodeTable extends BaseTableGateway
{
    protected $table = 'tbl_org_chart_node';
    protected $model = 'OrgChart\Model\ChartNode';
    protected $caches = null;
    protected $cache_prefix = array('chart_items_tree_');

    public function getNode($type = 0) //type = 0 get all object , type = 1 get All Array , type=2 get select Array
    {
        switch ($type) {
            case 0 :
                $data = $this->getAll(array('status' => 1));
                break;
            case 1 :
                $data = $this->getAll(array('status' => 1))->toArray();
                break;
            case 2 :
                $dataArray = array(0 => '-- Select --');
                $data = $this->getAll(array('status' => 1));
                if ($data->count()) {
                    foreach ($data as $row)
                        $dataArray[$row->id] = $row->title;
                }
                return $dataArray;
                break;
        }
        return $data;
    }

    public function getNodeList(Db\Sql\Select $select, $chartId, $parentId) //for data grid
    {
        $select
            ->group($this->table . '.id')
            ->join(array('u' => 'tbl_users'), $this->table . '.userId=u.id', array('displayName'), 'left')
            ->join(array('ch' => 'tbl_org_chart'), $this->table . '.chartId=ch.id', array('name'), 'left');
        $select->where(array($this->table . '.parentId' => $parentId));
        if ($chartId)
            $select->where(array($this->table . '.chartId' => $chartId));
        $select->order(array(
            $this->table . '.id'));

    }

    public function getTreeNode($chartId, $fieldName, $fields_table, $fields_api, $selectField)
    {
        $cache_key = 'chart_items_tree_' . $chartId;
        if (!$items = getCache()->getItem($cache_key)) {
            $this->swapResultSetPrototype();
            $select = $this->getSql()->select();
            $select
                ->group($this->table . '.id')
                ->join(array('u' => 'tbl_users'), $this->table . '.userId=u.id', array('displayName'), 'left')
                ->join(array('tf' => $fields_table), $this->table . '.userId=tf.entityId', $fieldName, 'left');
            $select->where(array($this->table . '.status' => 1, $this->table . '.chartId' => $chartId));
            $select->order(array(
                $this->table . '.parentId ASC'));
            $data = $this->selectWith($select);
            foreach ($data as $row) {
                $row['custom'] = $fields_api->generate($selectField, $row);
                $items[$row['parentId']][] = $row;
            }
            getCache()->setItem($cache_key, $items);
            $this->swapResultSetPrototype();
        }
        return $items;
    }

    public function getParentNodeArray($chartId)
    {
        $dataArray = array();
        if (!empty($chartId)) {
            $result = $this->getAll(array('status' => 1, 'chartId' => $chartId));
            if ($result->count())
                foreach ($result as $row)
                    $dataArray[$row->id] = $row->title;
        }
        return $dataArray;
    }

    public function remove($id)
    {
        $children = $this->getChildren($id);
        $ids = array_keys($children);
        parent::remove($ids);
    }

}
