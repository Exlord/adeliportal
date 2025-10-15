<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/17/12
 * Time: 11:18 AM
 * To change this template use File | Settings | File Templates.
 */

namespace User\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway;

class RoleTable extends BaseTableGateway
{
    const GUEST = 1;
    const MEMBER = 2;
    const ADMIN = 4;
    const SERVER_ADMIN = 3;

    const LEVEL_GUEST = 0;
    const LEVEL_MEMBER = 1;
    const LEVEL_ADMIN = 9000;
    const LEVEL_SERVER_ADMIN = 10000;

    protected $table = 'tbl_roles';
    protected $model = 'User\Model\Role';
    protected $caches = array(
        'all_active_roles',
        'user_counts_for_roles',
        'users_widget'
    );

    public static function getMemberRole($object = false)
    {
        if ($object) {
            $role = new Role();
            $role->level = 1;
            $role->id = 2;
            $role->roleName = 'Member';
            return $role;
        }
        return array('id' => self::MEMBER, 'roleName' => 'Member', 'level' => 1);
    }

    public function filterRoles($roles_data, $userId = null)
    {
        if (!$userId)
            $userId = current_user()->id;
        $currentMaxRoleLevel = (int)$this->getMaxLevel($userId);
        $roles_data = $this->getAll(array('id' => $roles_data));
        $roles = array();
        foreach ($roles_data as $role) {
            if ((int)$role->level <= $currentMaxRoleLevel)
                $roles[] = (int)$role->id;
        }
        return $roles;
    }

    public function getAllNested()
    {
        $list = $this->getAll(null, array('parentId ASC'));
        return $this->toNestedArray($list);
    }

    /**
     * Get roles with level <= current user's max role level
     * @param null|int|array $ids
     * @param bool|Db\Sql\Select $select
     * @param bool $equalLevel
     * @return array
     */
    public function getVisibleRoles($equalLevel = true, $ids = null, $select = false)
    {
        $where = array();
        if ($ids) {
            if (!is_array($ids))
                $ids = (int)$ids;
            $where[$this->primaryKey] = $ids;
        }

        $level = $equalLevel ? '<=' : '<';
        $where['level ' . $level . ' ?'] = $this->getMaxLevel(current_user()->id);
        $order = array('parentId ASC', 'level ASC');
        if (!$select) {
            $list = $this->getAll($where, $order);
            return $this->toNestedArray($list);
        } else {
            $select->where($where)->order($order);
        }
    }

    public function setVisibleRolesSelect(Db\Sql\Select $select)
    {
        $this->getVisibleRoles(true, null, $select);
    }

    public function getArray()
    {
        $cache_key = 'all_roles';
        $roles = array();
        if (!$roles = getCacheItem($cache_key)) {
            $list = $this->getAll(null, array('parentId ASC'));
            $roles_data = $this->toNestedArray($list);
            $roles = $this->makeIndentedArray($roles_data, 'roleName');

            setCacheItem($cache_key, $roles);
        }
        return $roles;
    }

    public function getArrayMember()
    {
        $cache_key = 'all_active_roles';
        $roles = array();
        if (!$roles = getCacheItem($cache_key)) {
            $list = $this->getAll(array(new \System\DB\Sql\Predicate\NotIn('level', array(0, 10000, 9000))), array('parentId ASC'));
            $roles_data = $this->toNestedArray($list);
            $roles = array();
            foreach ($roles_data as $key => $role) {
                $roles[$key] = ($role->indent ? '|' : '') . str_repeat('--', $role->indent) . $role->roleName;
            }
            setCacheItem($cache_key, $roles);
        }
        return $roles;
    }

    public function getCounts()
    {
        $cache_key = 'user_counts_for_roles';
        if (!$result = getCacheItem($cache_key)) {
            $this->swapResultSetPrototype();

            $currentMaxRoleId = $this->getMaxLevel(current_user()->id);

            $selectRoleId = new Select('tbl_roles');
            $selectRoleId->columns(array('id'))->where(array('level > ?' => $currentMaxRoleId));


            $select = $this->getSql()->select();
            $select->columns(array('roleName'))
                ->join(
                    array('ur' => 'tbl_users_roles'),
                    $this->table . '.id=ur.roleId',
                    array('count' => new \Zend\Db\Sql\Expression('COUNT(ur.userId)')), 'left')
                ->where->addPredicate(new NotIn($this->table . '.id', $selectRoleId));
            $select->group($this->table . '.roleName');

            $result = $this->selectWith($select)->toArray();
            $result[] = array(
                'roleName' => 'All User',
                'count' => getSM('user_table')->getAll()->count(),
            );
            $this->swapResultSetPrototype();
            setCacheItem($cache_key, $result);
        }
        return $result;
    }

    public function getRoleName()
    {
        $role = array('-1' => t('-- Select --'));
        $sql = $this->getSql();
        $select = $sql->select();
        // $select->where($where);
        $select->order(array('parentId ASC'));
        $select->columns(array('id', 'roleName'));
        $data = $this->selectWith($select);
        foreach ($data as $row) {
            $role[$row->id] = $row->roleName;
        }
        return $role;
    }

    public function getMaxLevel($userId)
    {
        $q = "SELECT MAX(level) AS level FROM `tbl_roles` WHERE id IN
                  (SELECT roleId FROM `tbl_users_roles` WHERE userId=?)";
        return $this->adapter->query($q, array($userId))->current()->level;
    }

    public function getHighestRole($userId)
    {
        $q = "SELECT id FROM `tbl_roles` WHERE level =
                (SELECT MAX(level) FROM `tbl_roles` WHERE id IN
                  (SELECT roleId FROM `tbl_users_roles` WHERE userId=?))";
        return $this->adapter->query($q, array($userId))->current()->id;
    }

    public function getRoleForSelect($equalLevel = true)
    {
        $roles = array();
        $select = $this->getVisibleRoles($equalLevel);
        foreach ($select as $row)
            $roles[$row->id] = $row->roleName;
        return $roles;
    }

    public function getRolesAdmin()
    {
        $dataArray = array();
        $select = $this->getAllNested();
        if ($select)
            foreach ($select as $row)
                if ($row->roleName != 'Web Master')
                    $dataArray[$row->id] = $row->roleName;
        return $dataArray;
    }

}