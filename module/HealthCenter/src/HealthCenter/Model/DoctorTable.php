<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/25/14
 * Time: 12:32 PM
 */

namespace HealthCenter\Model;


use System\DB\BaseTableGateway;
use System\DB\Sql\Select;
use Zend\Db\Sql\Expression;

class DoctorTable extends BaseTableGateway
{
    protected $table = 'tbl_users';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getSpecializations($doctorUserRoles = array())
    {
        $select = $this->getSql()->select();
        $select
            //no column from user table
            ->columns(array())
            //join user roles to filter by roleId
            ->join(array('ur' => 'tbl_users_roles'), $this->table . '.id=ur.userId', array())
            //join category entity to get category item count
            ->join(array('ie' => 'tbl_category_item_entity'),
                new Expression($this->table . '.id=ie.entityId AND ie.entityType="doctor_specializations"'),
                array(), 'LEFT')
            //join category item to get category item name
            ->join(array('ci' => 'tbl_category_item'), 'ie.itemId=ci.id',
                array('itemName', 'itemText', 'id',
                    'itemCount' => new Expression('COUNT(ie.entityId)')
                ), 'LEFT')
            ->where(array(
                'ur.roleId' => $doctorUserRoles,
                'ci.itemStatus' => 1
            ))
            ->group('ie.itemId');

        return $this->selectWith($select);
    }

    public function getDoctors($doctorUserRoles = array(), $spec = false)
    {
        $category_item_entity_join = new Expression($this->table . '.id=ie.entityId AND ie.entityType="doctor_specializations"');

        $select = $this->getSql()->select();
        $select
            //no column from user table
            ->columns(array('username', 'displayName', 'id'))
            //join user roles to filter by roleId
            ->join(array('ur' => 'tbl_users_roles'), $this->table . '.id=ur.userId', array())
            //join category entity to get category item count
            ->join(array('ie' => 'tbl_category_item_entity'),
                $category_item_entity_join,
                array(), 'LEFT')
            //join category item to get category item name
            ->join(array('ci' => 'tbl_category_item'), 'ie.itemId=ci.id',
                array(
                    'itemNames' => new Expression('GROUP_CONCAT(ci.itemName)'),
                    'itemIds' => new Expression('GROUP_CONCAT(ci.id)')
                ), 'LEFT')
            //join user profile to get name and image
            ->join(array('up' => 'tbl_user_profile'), $this->table . '.id=up.userId',
                array('firstName', 'lastName', 'image'), 'LEFT')
            ->where(array(
                'ur.roleId' => $doctorUserRoles,
            ))
            ->group($this->table . '.id');

        if ($spec) {
            $s2 = new Select('tbl_category_item_entity');
            $s2
                ->columns(array('entityId'))
                ->where(array('itemId' => $spec, 'entityType' => 'doctor_specializations'));
            $select->where->in($this->table . '.id', $s2);
        }

        return $this->selectWith($select);
    }

    public function getDoctorsList($doctorUserRoles)
    {
        $select = $this->getSql()->select();
        $select
            //no column from user table
            ->columns(array('username', 'displayName', 'id'))
            //join user roles to filter by roleId
            ->join(array('ur' => 'tbl_users_roles'), $this->table . '.id=ur.userId', array())
            //join category entity to get category item count
            //join user profile to get name and image
            ->join(array('up' => 'tbl_user_profile'), $this->table . '.id=up.userId',
                array('firstName', 'lastName', 'image'), 'LEFT')
            ->where(array(
                'ur.roleId' => $doctorUserRoles,
            ));

        $result = $this->selectWith($select);
        $doctors = array();
        if ($result && $result->count()) {
            foreach ($result as $row) {
                $doctors[$row['id']] = getUserDisplayName($row);
            }
        }
        return $doctors;
    }


}
