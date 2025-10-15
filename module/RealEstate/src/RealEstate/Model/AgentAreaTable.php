<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace RealEstate\Model;

use Application\API\App;
use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class AgentAreaTable extends BaseTableGateway
{
    protected $table = 'tbl_realestate_agent_area';
    protected $model = 'RealEstate\Model\AgentArea';
    protected $caches = null;
    protected $cache_prefix = array('agent_id_at_area_');
    protected $realEstateUrl = array();

    public function getAgentAreaArray($agentId)
    {

        $dataArray = array();
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        if (!empty($agentId)) {
            $sql = $this->getSql();
            $select = $sql
                ->select()
                ->join(array('a' => 'tbl_city_area_list'), $this->table . '.areaId=a.id', array('areaTitle'));
            $select->where(array($this->table . '.agentId' => (int)$agentId));
            $result = $this->selectWith($select);
            if ($result)
                foreach ($result as $row)
                    $dataArray[$row->id] = $row->areaTitle;
        }
        return $dataArray;
    }

    public function getAgentArray($areaId = 0)
    {
        $cacheKey = 'agent_id_at_area_' . $areaId;
        if ($list = getCache()->getItem($cacheKey))
            return $list;
        else {
            $agentUserRole = getConfig('real_estate_config')->varValue;
            if (isset($agentUserRole['agentUserRole']) && $agentUserRole['agentUserRole'])
                $agentUserRole = $agentUserRole['agentUserRole'];
            $userArray = getSM('user_table')->getByRoleId($agentUserRole, false, 'array', 1);

            if ($areaId) {
                $where = array(
                    'agentId' => array_keys($userArray),
                    'areaId' => $areaId,
                );

                $agentAreaSelect = $this->getAll($where);
                if ($agentAreaSelect)
                    foreach ($agentAreaSelect as $row)
                        if (isset($userArray[$row->agentId]))
                            $list[$row->agentId] = $userArray[$row->agentId];
            } else
                $list = $userArray;
           // getCache()->setItem($cacheKey, $list);
            return $list;
        }
    }

    public function removeAll($agentId)
    {
        if ($agentId)
            $this->delete(array('agentId' => $agentId));
    }
}


