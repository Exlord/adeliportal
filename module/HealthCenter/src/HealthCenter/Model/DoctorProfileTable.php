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
use Zend\Stdlib\Hydrator\ClassMethods;

class DoctorProfileTable extends BaseTableGateway
{
    protected $table = 'tbl_hc_doctor';
    protected $primaryKey = 'doctorId';
    protected $caches = null;
    protected $cache_prefix = null;

    public function get($doctorId)
    {
        $result = $this->select(array('doctorId' => $doctorId));

        if ($result) {
            $result = $result->current();
            if ($result)
                return $result;
            else {
                //if this doctors profile has not been created make a new one and insert
                $this->insert(array('doctorId' => $doctorId));
                return $this->get($doctorId);
            }
        }
        return null;
    }

}