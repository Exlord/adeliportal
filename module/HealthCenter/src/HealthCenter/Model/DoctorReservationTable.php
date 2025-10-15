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

class DoctorReservationTable extends BaseTableGateway
{
    protected $table = 'tbl_hc_doctor_reservation';
    protected $caches = null;
    protected $cache_prefix = null;

    public static $ReserveCodes = array(
        '0' => 'Not Finalized',
        '1' => 'Reserved',
        '2' => 'Failed Reservation',
        '3' => 'Cancel Request',
        '4' => 'Canceled',
        '5' => 'Visited',
    );

    public function getReserver($id, $multiple = false)
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('id', 'userId', 'timeId', 'doctorId', 'date', 'status', 'note', 'paymentStatus', 'paymentId'))
            ->join(array('u' => 'tbl_users'), $this->table . '.userId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->join(array('u2' => 'tbl_users'), $this->table . '.doctorId=u2.id', array('d_username' => 'username', 'd_displayName' => 'displayName', 'd_email' => 'email'), 'LEFT')
            ->join(array('up2' => 'tbl_user_profile'), 'up2.userId=u2.id', array('d_firstName' => 'firstName', 'd_lastName' => 'lastName', 'd_mobile' => 'mobile'), 'LEFT')
            ->join(array('t' => 'tbl_hc_doctor_timetable'), $this->table . '.timeId=t.id', array('start'))
            ->where(array(
                $this->table . '.id' => $id,
            ));

        $result = $this->selectWith($select);
        if (!$multiple)
            $result = $result->current();
        $this->swapResultSetPrototype();
        return $result;
    }

    public function getReserves($day = null)
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('id', 'userId', 'timeId', 'status',))
            ->join(array('u' => 'tbl_users'), $this->table . '.userId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->join(array('t' => 'tbl_hc_doctor_timetable'), $this->table . '.timeId=t.id', array('start', 'end'))
            ->where(array());
        if ($day)
            $select->where(array('t.date' => $day,));

        $result = $this->selectWith($select);
        $this->swapResultSetPrototype();
        return $result;
    }

    public function getMyLastSession()
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('userId'))
            ->join(array('t' => 'tbl_hc_doctor_timetable'), $this->table . '.timeId=t.id',
                array('start', 'end', 'date'))
            ->join(array('u' => 'tbl_users'), $this->table . '.doctorId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->where(array(
                $this->table . '.userId' => current_user()->id,
                $this->table . '.status' => 5,
                't.end < ?' => time()
            ))
            ->order('t.start DESC')
            ->limit(1);

        $result = $this->selectWith($select);
        $this->swapResultSetPrototype();
        return $result;
    }

    public function getMyNextSession()
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('userId'))
            ->join(array('t' => 'tbl_hc_doctor_timetable'), $this->table . '.timeId=t.id',
                array('start', 'end', 'date'))
            ->join(array('u' => 'tbl_users'), $this->table . '.doctorId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->where(array(
                $this->table . '.userId' => current_user()->id,
                $this->table . '.status' => 1,
                't.start > ?' => time()
            ))
            ->order('t.start ASC')
            ->limit(1);

        $result = $this->selectWith($select);
        $this->swapResultSetPrototype();
        return $result;
    }

    public function cancel($id, $status, $note)
    {
        $this->update(array('status' => $status, 'note' => new \Zend\Db\Sql\Expression('CONCAT(IFNULL(' . $this->qi('note') . ',""), ' . $this->qv($note) . ', "\n")')), array('id' => $id));
    }

    public function getReserveCount($userId)
    {
        $select = $this->getSql()->select();
        $select
            ->columns(array(
                'status', 'count' => new Expression('COUNT(status)')
            ))
            ->where(array('userId' => $userId))
            ->group('status');
//        print $select->getSqlString($this->getAdapter()->getPlatform());
//        die;
        return $this->selectWith($select);
    }

    public function removeByDoctor($doctorId)
    {
        $this->delete(array('doctorId' => $doctorId));
    }

    public function setPaymentFailed()
    {
        $config = getConfig('health-center')->varValue;
        if (isset($config['reservationPaymentTimeout']))
            $reservationPaymentTimeout = $config['reservationPaymentTimeout'];
        else
            $reservationPaymentTimeout = 60;

        $update = $this->getSql()->update();
        $update
            ->set(array(
                'status' => 2
            ))
            ->where(array(
                'status' => 0,
                'date < ?' => time() - ($reservationPaymentTimeout * 60)
            ));
        $this->updateWith($update);
        //TODO should i notify anyone about this ?
    }
}