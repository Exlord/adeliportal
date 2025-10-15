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

class WorkshopClassTable extends BaseTableGateway
{
    const CLASS_REGISTER_TIMEOUT = 48;
    const CLASS_START_NOTIFY_TIME = 48;

    protected $table = 'tbl_ec_workshop_class';
    protected $model = 'EducationalCenter\Model\WorkshopClass';
    protected $caches = null;
    protected $cache_prefix = null;

    /**
     * @param $id
     * @return WorkshopClass|array
     */
    public function getItem($id)
    {
        return parent::get($id);
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null|Db\ResultSet\HydratingResultSet|Db\ResultSet\ResultSet
     */
    public function get($id)
    {
        if (!is_array($id))
            $id = array($id);

        array_walk($id, function (&$item, $index) {
            $item = (int)$item;
        });

        $select = $this->getSql()->select();
        $select
            ->join(array('w' => 'tbl_ec_workshop'), $this->table . '.workshopId=w.id', array('workshopTitle' => 'title'))
            ->join(array('u' => 'tbl_users'), $this->table . '.educatorId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->where(array($this->table . '.id' => $id));
        $result = $this->selectWith($select);
        return $result;
    }

    public function getAvailable($workshop, $page = 1, $perPage = 20)
    {
//        $usedCapacity = new Db\Sql\Select('tbl_ec_workshop_attendances');
//        $usedCapacity->columns(array(new Db\Sql\Predicate\Expression('COUNT(id)')));
//        $usedCapacity->where(array('tbl_ec_workshop_attendances.classId' => new Db\Sql\Predicate\Expression($this->table . '.id')));
//
//        $firstSession = new Db\Sql\Select('tbl_ec_workshop_timetable');
//        $firstSession->columns(array('start'))
//            ->where(array(
//                'tbl_ec_workshop_timetable.classId' => new Db\Sql\Predicate\Expression($this->table . '.id'),
//                'status' => 0
//            ))
//            ->order('tbl_ec_workshop_timetable.start ASC')
//            ->limit(1);

        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(
                array('id', 'workshopId', 'educatorId', 'title', 'note', 'capacity', 'price', 'status',
                    'usedCapacity' => new Db\Sql\Predicate\Expression("(SELECT COUNT(id) FROM `tbl_ec_workshop_attendance` WHERE `classId` = `tbl_ec_workshop_class`.`id`)"),
                    'firstSession' => new Db\Sql\Predicate\Expression("(SELECT MIN(`start`) FROM `tbl_ec_workshop_timetable` WHERE `classId` = `tbl_ec_workshop_class`.`id` AND `status` = '0')"),
                    'lastSession' => new Db\Sql\Predicate\Expression("(SELECT MAX(`end`) FROM `tbl_ec_workshop_timetable` WHERE `classId` = `tbl_ec_workshop_class`.`id` AND `status` = '0')")
                ))
            ->join(array('u' => 'tbl_users'), $this->table . '.educatorId=u.id', array('username', 'displayName'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName'), 'LEFT')
            ->where(array(
                $this->table . '.status' => 1,
                $this->table . '.workshopId' => $workshop,
            ));

        return $this->getPaginated($select, $sql, $page, $perPage);
    }

    public function getAllClasses($page = 1, $perPage = 20)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(
                array('id', 'workshopId', 'educatorId', 'title', 'note', 'capacity', 'price', 'status',
                    'usedCapacity' => new Db\Sql\Predicate\Expression("(SELECT COUNT(id) FROM `tbl_ec_workshop_attendance` WHERE `classId` = `tbl_ec_workshop_class`.`id`)"),
                    'firstSession' => new Db\Sql\Predicate\Expression("(SELECT MIN(`start`) FROM `tbl_ec_workshop_timetable` WHERE `classId` = `tbl_ec_workshop_class`.`id` AND `status` = '0')"),
                    'lastSession' => new Db\Sql\Predicate\Expression("(SELECT MAX(`end`) FROM `tbl_ec_workshop_timetable` WHERE `classId` = `tbl_ec_workshop_class`.`id` AND `status` = '0')")
                ))
            ->join(array('u' => 'tbl_users'), $this->table . '.educatorId=u.id', array('username', 'displayName'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName'), 'LEFT')
            ->where(array(
                $this->table . '.status <> ?' => 0,
            ));

        return $this->getPaginated($select, $sql, $page, $perPage);
    }

    public function removeByWorkshop($workshop)
    {
        $classes = $this->select(array('workshopId' => $workshop));
        if ($classes && $classes->count()) {
            $ids = array();
            foreach ($classes as $row) {
                $ids[] = $row->id;
            }
            $this->remove($ids);
        }
    }

    public function remove($id)
    {
        getSM('ec_workshop_timetable')->removeByClass($id);
        getSM('ec_workshop_attendance_table')->removeByClass($id);
        parent::remove($id);
    }

    public function getClassesForCronUpdate()
    {
        $select = $this->getSql()->select();
        $select
            ->columns(array('id', 'title', 'workshopId', 'status'))
            ->join(array('wt' => 'tbl_ec_workshop_timetable'), $this->table . '.id=wt.classId',
                array(
                    new Db\Sql\Predicate\Expression('MIN(wt.start) as firstSession'),
                    new Db\Sql\Predicate\Expression('MAX(wt.end) as lastSession')))
            ->join(array('w' => 'tbl_ec_workshop'), $this->table . '.workshopId=w.id', array('workshopTitle' => 'title'))
            ->where(array($this->table . '.status' => array(1, 3)))
            ->group($this->table . '.id');

        return $this->select($select);
    }
}
