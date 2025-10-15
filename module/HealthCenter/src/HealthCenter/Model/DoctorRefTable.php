<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/27/14
 * Time: 11:00 AM
 */

namespace HealthCenter\Model;

use System\DB\BaseTableGateway;
use System\Model\BaseModel;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Stdlib\Hydrator\ClassMethods;

class DoctorRefTable extends BaseTableGateway
{
    protected $table = 'tbl_hc_doctor_ref';
    protected $caches = null;
    protected $cache_prefix = null;

    /**
     * Assign doctor to patient
     * @param $doctorId
     * @param $patientId
     * @param int $refId the user that assigned or referred this patient to this doctor
     * @throws \Exception
     */
    public function setDoctor($doctorId, $patientId, $refId = 0)
    {
        try {
            $this->insert(array('doctorId' => $doctorId, 'patientId' => $patientId, 'refId' => $refId));
        } catch (\Exception $e) {
            //duplicate entry
            if (!($e->getPrevious() && $e->getPrevious()->getCode() == '23000'))
                throw $e;
        }
    }

    public function isMyDoctor($doctorId, $patientId)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array('count' => new Expression('COUNT(doctorId)')))
            ->where(array(
                'doctorId' => $doctorId,
                'patientId' => $patientId,
            ));
        $result = $this->selectWith($select);
        if ($result) {
            $result = $result->current();
            if ($result)
                return $result['count'] > 0;
        }
        return false;
    }

    public function removeByDoctor($doctorId)
    {
        $this->delete(array('doctorId' => $doctorId));
    }
}