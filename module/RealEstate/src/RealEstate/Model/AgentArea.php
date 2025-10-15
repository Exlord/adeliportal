<?php
namespace RealEstate\Model;

use System\Model\BaseModel;

class AgentArea extends BaseModel
{
    public $id;
    public $agentId;
    public $areaId;

    /**
     * @param mixed $agentId
     */
    public function setAgentId($agentId)
    {
        $this->agentId = $agentId;
    }

    /**
     * @return mixed
     */
    public function getAgentId()
    {
        return $this->agentId;
    }

    /**
     * @param mixed $areaId
     */
    public function setAreaId($areaId)
    {
        $this->areaId = $areaId;
    }

    /**
     * @return mixed
     */
    public function getAreaId()
    {
        return $this->areaId;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}
