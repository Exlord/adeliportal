<?php
namespace OrgChart\Model;

use System\DB\BaseTableGateway;

class OrgChartTable extends BaseTableGateway
{
    protected $table = 'tbl_org_chart';
    protected $model = 'OrgChart\Model\OrgChart';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getChart($type = 0) //type = 0 get all object , type = 1 get All Array , type=2 get select Array
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
                        $dataArray[$row->id] = $row->name;
                }
                return $dataArray;
                break;
        }
        return $data;
    }

    public function getById($id,$type=0)//$type = 0 only 1 row , $type = 1 much row
    {
        $data='';
        switch($type)
        {
            case 0 :
                $data = $this->getAll(array('status'=>1,'id'=>$id))->current();
                break;
            case 1 :
                $data = $this->getAll(array('status'=>1,'id'=>$id));
                break;
        }
        return $data;
    }

    public function search($term)
    {
        $where = array(
            'status' => 1,
            'name like ?' => '%' . $term . '%',
        );
        return $this->getAll($where, array('name ASC'));
    }
}
