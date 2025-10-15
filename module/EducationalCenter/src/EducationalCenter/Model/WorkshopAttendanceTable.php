<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace EducationalCenter\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;
use Zend\Stdlib\ArrayObject;

class WorkshopAttendanceTable extends BaseTableGateway
{
    const  CLASS_PAYMENT_TIMEOUT = 60;

    protected $table = 'tbl_ec_workshop_attendance';
    protected $model = 'EducationalCenter\Model\WorkshopAttendance';
    protected $caches = null;
    protected $cache_prefix = null;

    public static $RegisterStatus = array(
        '0' => 'Temporarily reserved, waiting for payment',
        '1' => 'Reserved',
        '2' => 'Failed Reservation, payment was not done in time',
        '3' => 'Cancel Request',
        '4' => 'Canceled',
    );

    public function getAttendances($classId, $status = array(0, 1))
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('id', 'userId', 'classId', 'registerDate', 'status',))
            ->join(array('u' => 'tbl_users'), $this->table . '.userId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->where(array(
                $this->table . '.status' => $status,
                $this->table . '.classId' => $classId,
            ));

        return $this->selectWith($select);
    }

    public function isRegistered($classId, $userId)
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('count' => new Db\Sql\Predicate\Expression('COUNT(id)')))
            ->where(array(
                'classId' => $classId,
                'userId' => $userId,
                'status' => array(0, 1, 3)
            ));

        $result = $this->selectWith($select)->current();
        $this->swapResultSetPrototype();
        return ((int)$result['count'] > 0);
    }

    public function getRegisteredCount($classId)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('count' => new Db\Sql\Predicate\Expression('COUNT(id)')))
            ->where(array('classId' => $classId));
        return $this->selectWith($select)->current()->count;
    }

    public function getAttendance($id)
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('id', 'userId', 'classId', 'registerDate', 'status', 'note'))
            ->join(array('u' => 'tbl_users'), $this->table . '.userId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->join(array('c' => 'tbl_ec_workshop_class'), $this->table . '.classId=c.id', array('title', 'educatorId', 'workshopId'), 'LEFT')
            ->join(array('w' => 'tbl_ec_workshop'), 'w.id=c.workshopId', array('workshopTitle' => 'title'), 'LEFT')
            ->join(array('u2' => 'tbl_users'), 'c.educatorId=u.id', array('e_username' => 'username', 'e_displayName' => 'displayName', 'e_email' => 'email'), 'LEFT')
            ->join(array('up2' => 'tbl_user_profile'), 'up2.userId=u2.id', array('e_firstName' => 'firstName', 'e_lastName' => 'lastName', 'e_mobile' => 'mobile'), 'LEFT')
            ->where(array(
                $this->table . '.id' => $id,
            ));

        $result = $this->selectWith($select)->current();
        $this->swapResultSetPrototype();
        return $result;
    }

    public function getAttendanceForCancel($id)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('id', 'userId', 'classId', 'registerDate', 'status', 'paymentId', 'paymentStatus', 'note',
                'firstSession' => new Db\Sql\Predicate\Expression("(SELECT MIN(`start`) FROM `tbl_ec_workshop_timetable` WHERE `classId` = `c`.`id` AND `status` = '0')"),))
            ->join(array('u' => 'tbl_users'), $this->table . '.userId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->join(array('c' => 'tbl_ec_workshop_class'), $this->table . '.classId=c.id', array('classTitle' => 'title', 'educatorId', 'workshopId', 'price'), 'LEFT')
            ->join(array('w' => 'tbl_ec_workshop'), 'w.id=c.workshopId', array('workshopTitle' => 'title'), 'LEFT')
            ->where(array(
                $this->table . '.id' => $id,
            ));

        $result = $this->selectWith($select)->current();
        return $result;
    }

    public function cancel($id, $status, $note)
    {
        $this->update(array('status' => $status, 'note' => new Db\Sql\Expression('CONCAT(IFNULL(' . $this->qi('note') . ',""), ' . $this->qv($note) . ', "\n")')), array('id' => $id));
    }

    public function removeByClass($class)
    {
        $this->delete(array('classId' => $class));
    }

    public function setPaymentFailed()
    {
        $config = getConfig('educational-center')->varValue;
        if (isset($config['classPaymentTimeout']))
            $classPaymentTimeout = $config['classPaymentTimeout'];
        else
            $classPaymentTimeout = self::CLASS_PAYMENT_TIMEOUT;

        $update = $this->getSql()->update();
        $update
            ->set(array(
                'status' => 2
            ))
            ->where(array(
                'status' => 0,
                'registerDate < ?' => time() - ($classPaymentTimeout * 60)
            ));
        $this->updateWith($update);
        //TODO should i notify anyone about this ?
    }

    public function getUserRegisterCount($userId)
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(array('count' => new Db\Sql\Predicate\Expression('COUNT(status)'), 'status'))
            ->where(array('userId' => $userId));
        $result = $this->selectWith($select);
        $this->swapResultSetPrototype();
        return $result;
    }
}
