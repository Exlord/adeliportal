<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace HealthCenter\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;
use Zend\Stdlib\ArrayObject;

class DoctorTimeTable extends BaseTableGateway
{
    protected $table = 'tbl_hc_doctor_timetable';
    protected $model = 'HealthCenter\Model\DoctorTime';
    protected $caches = null;
    protected $cache_prefix = null;

    public function removeByDoctor($doctorId)
    {
        $this->delete(array('doctorId' => $doctorId));
    }

    public function findFirstUnreserved($doctor)
    {
        $now = time();
        $select = $this->getSql()->select();
        $select
            ->columns(array('date'))
            ->join(array('dr' => 'tbl_hc_doctor_reservation'), $this->table . '.id=dr.timeId', array(), 'LEFT')
            ->where(array(
                $this->table . '.doctorId' => $doctor,
                $this->table . '.start > ?' => $now,
            ))
            ->order($this->table . '.start ASC')
            ->limit(1);
        $select->where->isNull('dr.status');

        return $this->selectWith($select);
    }

    public function getDates($doctor, $day)
    {
        $select = $this->getSql()->select();
        $select
            ->columns(array('id', 'doctorId', 'date', 'start', 'end', 'status'))
            ->join(array('dr' => 'tbl_hc_doctor_reservation'), $this->table . '.id=dr.timeId', array('resStatus' => 'status', 'userId', 'resId' => 'id'), 'LEFT')
            ->where(array(
                $this->table . '.doctorId' => $doctor,
                $this->table . '.date' => $day,
            ))
            ->order($this->table . '.start ASC');
//        $select->where->isNull('dr.status');

        return $this->selectWith($select);
    }

    public function getItem($id)
    {
        return parent::get($id);
    }

    public function get($time, $doctor = null)
    {
        $where = array(
            $this->table . '.id' => $time,
        );
        if ($doctor)
            $where[$this->table . '.doctorId'] = $doctor;

        $select = $this->getSql()->select();
        $select
            ->columns(array('start', 'end', 'status', 'id', 'doctorId', 'date'))
            ->join(array('r' => 'tbl_hc_doctor_reservation'), $this->table . '.id=r.timeId', array('resStatus' => 'status', 'userId'), 'LEFT')
            ->where($where);

        $result = $this->selectWith($select);
        if ($result) {
            if (is_array($time)) {
                if ($result->count())
                    return $result;
            } else {
                $result = $result->current();
                if ($result)
                    return $result;
            }
        }

        return null;
    }

    public function search($dateFrom, $dateTo, $doctor = null, $spec = null, $start = null)
    {
        $this->swapResultSetPrototype();
        $where = array(
            $this->table . '.status' => 0,
            $this->table . '.date >= ?' => $dateFrom,
            $this->table . '.date <= ?' => $dateTo,
        );
        if ($doctor)
            $where[$this->table . '.doctorId'] = $doctor;
        if ($spec)
            $where['ci.id'] = $spec;
        //TODO how ? timestamp
        if ($start)
            $where[$this->table . '.start >= ?'] = $start;

        $category_item_entity_join = new Db\Sql\Predicate\Expression($this->table . '.doctorId=ie.entityId AND ie.entityType="doctor_specializations"');

        $select = $this->getSql()->select();
        $select
            ->columns(array('id', 'date', 'start', 'end', 'doctorId'))
            ->join(array('u' => 'tbl_users'), $this->table . '.doctorId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->join(array('r' => 'tbl_hc_doctor_reservation'), $this->table . '.id=r.timeId', array('reserverId' => 'userId', 'status', 'paymentStatus'), 'LEFT')
            //join category entity to get category item count
            ->join(array('ie' => 'tbl_category_item_entity'), $category_item_entity_join, array(), 'LEFT')
            //join category item to get category item name
            ->join(array('ci' => 'tbl_category_item'), 'ie.itemId=ci.id',
                array(
                    'itemNames' => new Db\Sql\Predicate\Expression('GROUP_CONCAT(ci.itemName)'),
                    'itemIds' => new Db\Sql\Predicate\Expression('GROUP_CONCAT(ci.id)')
                ), 'LEFT')
            ->where($where)
            ->order($this->table . '.start ASC')
            ->group($this->table . '.id');

        $result = $this->selectWith($select);
        $this->swapResultSetPrototype();
        return $result;
    }
}
