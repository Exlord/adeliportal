<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 2:07 PM
 */
namespace User\Model;

use System\DB\BaseTableGateway;
use User\Permissions\Acl\Acl;
use Zend\Db;
use Zend\Db\TableGateway;

class UserRoleTable extends BaseTableGateway
{
    protected $table = 'tbl_users_roles';
    protected $model = 'User\Model\UserRole';
    protected $caches = array('all_active_roles', 'user_counts_for_roles', 'users_widget');

    public function changeRoles($userId, $roles)
    {
        $this->removeByUserId($userId);
        if ($roles && is_array($roles))
            foreach ($roles as $role) {
                $this->insert(array('userId' => $userId, 'roleId' => $role));
            }
        elseif ($roles && !is_array($roles))
            $this->insert(array('userId' => $userId, 'roleId' => $roles));
    }

    public function removeByUserId($userId)
    {
//        $this->_clearCache();
        $this->delete(array('userId' => $userId));
    }

    public function getRoles($userId)
    {
        $_resultSetPrototype = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $select = $this->sql->select()
            ->columns(array(), false)
            ->join(array('r' => 'tbl_roles'), $this->table . '.roleId = r.id', array('id', 'roleName'))
            ->where(array($this->table . '.userId' => $userId));
        $data = $this->selectWith($select)->toArray();
        // var_dump($data);
        $this->resultSetPrototype = $_resultSetPrototype;
        return $data;
    }

    /**
     * @param $uid int userId
     * @return array
     */
    public function getRolesArray($uid)
    {
        $roles_data = $this->getAll(array('userId' => $uid));
        $roles = array();
        foreach ($roles_data as $row) {
            $roles[] = $row->roleId;
        }
        return $roles;
    }

    public function getRolesByUserId($userId)
    {
        return $this->getAll(array('userId' => $userId))->toArray();
    }

}
