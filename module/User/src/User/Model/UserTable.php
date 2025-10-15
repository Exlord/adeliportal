<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/17/12
 * Time: 11:18 AM
 * To change this template use File | Settings | File Templates.
 */

namespace User\Model;

use System\Model\BaseModel;
use User\API\Flood as FloodApi;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway;
use System;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Stdlib\Hydrator\ClassMethods;

class UserTable extends \System\DB\BaseTableGateway
{
    protected $table = 'tbl_users';
    protected $model = 'User\Model\User';
    protected $caches = array('user_counts_for_roles', 'users_widget');

    public static $emailStatus = array(
        0 => 'Unknown',
        1 => 'Validation Email Sent',
        2 => 'Validated'
    );

    public static $accountStatus = array(
        0 => 'Not Approved',
        1 => 'Approved',
        2 => 'Temporary Locked',
        3 => 'Locked',
        4 => 'Banned',
        5 => 'Deleted',
    );

    public static $profileFields = array(
        'table' => array('id', 'username', 'displayName', 'email'),
        'profile' => array('firstName', 'lastName', 'mobile')
    );

//    public static $userStatus = array(
//        0 => 'Unknown',
//        1 => 'Active',
//        2 => 'Temporary Locked',
//        3 => 'Locked',
//        4 => 'Banned',
//        5 => 'Deleted'
//    );

    public function getByUsername($username)
    {
        $rowset = $this->select(array('username' => $username));
        if (!$rowset) {
            throw new \Exception("Could not find username $username");
        }
        return $rowset;
    }

    public function getByEmail($email)
    {
        $rowset = $this->select(array('email' => $email));
        if (!$rowset) {
            throw new \Exception("Could not find User with email : $email");
        }
        return $rowset;
    }

    public function getByEmailHash($hash)
    {
        $rowset = $this->select(array('SHA1(email)' => $hash));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find User with email hash : $hash");
        }
        return $row;
    }

    public function authenticate($username, $password) // TODO Checked Status
    {
        $authAdapter = $this->getServiceLocator()->get('user_auth_adapter');
        $authAdapter->setIdentity($username);
        $authAdapter->setCredential($password);

        /* @var $authService \Zend\Authentication\AuthenticationService */
        $authService = $this->getServiceLocator()->get('user_auth_service');
        $result = $authService->authenticate($authAdapter);

        switch ($result->getCode()) {

            case \Zend\Authentication\Result::FAILURE_IDENTITY_NOT_FOUND:
            case \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID:
                FloodApi::$FloodCount++;
                $ip = getSM('Request')->getServer('REMOTE_ADDR');
                getSM('user_flood_table')->save(array(
                    'ip' => $ip,
                    'timestamp' => time(),
                    'username' => $username
                ));
                return $result->getMessages();
                break;

            case 'DISABLED':
                return $result->getMessages();
                break;
            case \Zend\Authentication\Result::SUCCESS:
                $user = $authAdapter->getResultRowObject(null, 'password');
                $user->data = @unserialize($user->data);
                $user->roles = $this->getServiceLocator()->get('user_role_table')->getRoles($user->id);
                return $user;
                break;

            default:
                return 'Invalid Credential Provided !';
                break;
        }
    }

    public function login($user)
    {
        $user = $this->authenticate($user->username, $user->password);
        if (is_object($user)) {
            getSM()->get('user_auth_service')->getStorage()->write($user);
            getSM()->get('session_manager')->rememberMe();
            $this->setLoginForUser($user);
        }
        return $user;
    }

    /**
     * @param $id
     * @param bool|Array $columns
     * @return array|\ArrayObject|null|object|Db\ResultSet\HydratingResultSet|Db\ResultSet\ResultSet
     */
    public function getUser($id, $columns = false)
    {
        if (!$columns) {
            $user = parent::get($id);
            $user->data = @unserialize($user->data);
            if (!$user->data || !is_array($user->data))
                $user->data = array();
            return $user;
        } else {
            $this->swapResultSetPrototype();
            $select = $this->getSql()->select();

            if (isset($columns['table']))
                $select->columns($columns['table']);
            else
                $select->columns(array('id', 'username', 'email', 'displayName'));

            if (isset($columns['profile']))
                $select->join(array('up' => 'tbl_user_profile'), $this->table . '.id=up.userId',
                    $columns['profile'], "LEFT");

            if (isset($columns['roles'])) {
                $select->join(array('ur' => 'tbl_users_roles'), $this->table . '.id=ur.userId', array('roleId' => new Db\Sql\Expression('GROUP_CONCAT(ur.roleId)')))
                    ->join(array('r' => 'tbl_roles'), 'ur.roleId=r.id', array('roleName' => new Db\Sql\Expression('GROUP_CONCAT(r.roleName)')));
            }
            if (isset($columns['address'])) {
                $select
                    ->join(array('c' => 'tbl_city_list'), 'up.cityId=c.id', array('cityTitle'), \Zend\Db\Sql\Select::JOIN_LEFT)
                    ->join(array('s' => 'tbl_state_list'), 'up.stateId=s.id', array('stateTitle'), \Zend\Db\Sql\Select::JOIN_LEFT)
                    ->join(array('co' => 'tbl_country_list'), 'up.countryId=co.id', array('countryTitle'), \Zend\Db\Sql\Select::JOIN_LEFT);
            }
            $select->where(array($this->table . '.id' => $id));

            $result = $this->selectWith($select);
            $this->swapResultSetPrototype();
            if ($result) {
                $result = $result->current();
                if ($result) {
                    if (isset($columns['roles'])) {
                        $result['roleId'] = explode(',', $result['roleId']);
                        $result['roleName'] = explode(',', $result['roleName']);
                    }
                    if (isset($result['data']))
                        $result['data'] = unserialize($result['data']);
                    return $result;
                }
            }
            return array();
        }
    }

    public function get($id, $fields_table = null)
    {
        $protoType = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $select = $this->getSql()->select();
        $select
            ->columns(array('userId' => 'id', 'email', 'displayName', 'username', 'emailStatus', 'data'))
            ->join(array('ur' => 'tbl_users_roles'), $this->table . '.id=ur.userId', array('roleId' => new Db\Sql\Expression('GROUP_CONCAT(ur.roleId)')))
            ->join(array('r' => 'tbl_roles'), 'ur.roleId=r.id', array('roleName' => new Db\Sql\Expression('GROUP_CONCAT(r.roleName)')))
            ->join(array('up' => 'tbl_user_profile'), $this->table . '.id=up.userId',
                array('firstName', 'lastName', 'phone', 'mobile', 'address', 'aboutMe', 'image', 'birthDate'), \Zend\Db\Sql\Select::JOIN_LEFT)
            ->join(array('c' => 'tbl_city_list'), 'up.cityId=c.id', array('cityTitle'), \Zend\Db\Sql\Select::JOIN_LEFT)
            ->join(array('s' => 'tbl_state_list'), 'up.stateId=s.id', array('stateTitle'), \Zend\Db\Sql\Select::JOIN_LEFT)
            ->join(array('co' => 'tbl_country_list'), 'up.countryId=co.id', array('countryTitle'), \Zend\Db\Sql\Select::JOIN_LEFT)
            ->where(array($this->table . '.id' => $id));
        if ($fields_table)
            $select->join(array('tf' => $fields_table), $this->table . '.id=tf.entityId', array('*'), \Zend\Db\Sql\Select::JOIN_LEFT);
        $result = $this->selectWith($select)->current();
        $result['data'] = @unserialize($result['data']);
        if (!$result['data'] || !is_array($result['data']))
            $result['data'] = array();
        $this->resultSetPrototype = $protoType;
        return $result;
    }

    public function getByRoleId($roleId, $withMaxLevel = true, $returnType = 'array', $pageNumber = false)
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $this->getSql()->select();
        $select
            ->join(array('ur' => 'tbl_users_roles'), 'tbl_users.id=ur.userId', array('roleId'), 'left')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=tbl_users.id', array('image', 'phone', 'mobile', 'address'), 'left')
            // ->join(array('r' => 'tbl_roles'), 'ur.roleId=r.id', array('roleName' => new Expression('GROUP_CONCAT(r.roleName)')), 'left')
            ->where(array('roleId' => $roleId, $this->table . '.accountStatus' => 1))
            // ->group('tbl_users.username')
            ->order(array('tbl_users.id ASC'));

        if ($withMaxLevel) {
            $currentMaxRoleId = getSM('role_table')->getMaxLevel(current_user()->id);
            $selectRoleId = new Select('tbl_roles');
            $selectRoleId->columns(array('id'))->where(array('level > ?' => $currentMaxRoleId));

            $selectUserId = new Select('tbl_users_roles');
            $selectUserId->columns(array('userId'))->where->in('roleId', $selectRoleId);

            $select->where->addPredicate(new NotIn($this->table . '.id', $selectUserId));
        }

        if ($pageNumber) {
            $result = $this->getPaginated($select, $sql, $pageNumber, 21);
        } else {
            $result = $this->selectWith($select);
        }

        if ($returnType == 'array') {
            $list = array();
            foreach ($result as $user) {
                $list[$user['id']] = getUserDisplayName($user);
            }
        } else
            $list = $result;
        $this->swapResultSetPrototype();
        return $list;
    }

    public function getEmailsByRoleId($roleId)
    {
        $protoType = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $select = $this->getSql()->select();
        $select
            ->columns(array('email' => 'email', 'displayName', 'username'))
            ->join(array('ur' => 'tbl_users_roles'), $this->table . '.id=ur.userId')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=ur.userId', array('firstName', 'lastName'), \Zend\Db\Sql\Select::JOIN_LEFT)
            ->where(array('ur.roleId' => $roleId));
        $result = $this->selectWith($select);
        $list = array();
        foreach ($result as $user) {
            $list[$user->email] = $user->username;
        }
        $this->resultSetPrototype = $protoType;
        return $list;
    }

    public function getForEdit($id)
    {
        $user = $this->getUser($id);
        $profile = getSM()->get('user_profile_table')->getByUserId($id);
        $roles = getSM()->get('user_role_table')->getRolesArray($id);
        return array(
            'basic' => $this->toArray($user),
            'profile' => $this->toArray($profile),
            'roles' => $roles
        );
    }

//    public function save($account)
//    {
//        if (isset($account['account']['roles'])) {
//            $user_role = getSM()->get('user_role_table');
//            $user_role->changeRoles($account['account']['id'], $account['account']['roles']);
//            unset($account['account']['roles']);
//        }
//        parent::save($account['account']);
//        getSM()->get('user_profile_table')->save($account['profile']);
//    }

    public function checkPassword($userId, $old_password)
    {
        $row = $this->select(array('id' => $userId));
        if (!$row)
            return false;
        $row = $row->current();
        if (!$row)
            return false;

        $bcrypt = new Bcrypt();
        return $bcrypt->verify($old_password, $row->password);
    }

    public function changePassword($userId, $newPassword)
    {
        $bcrypt = new Bcrypt();
        $this->update(array('password' => $bcrypt->create($newPassword)), array('id' => $userId));
    }

    public function getOnlineUsersCount()
    {

    }

    public function setLoginForUser($user)
    {
        $this->update(array(
            'lastLoginDate' => New Expression('loginDate'),
            'loginDate' => time(),
        ), array('id' => $user->id));
//        $select = $this->getAll(array('username' => $username))->current();
//        return $select->id;
    }

    //type = 0 get all object , type = 1 get All Array , type=2 get select Array
    public function getUsers($type = 0)
    {
        switch ($type) {
            case 0 :
                $data = $this->getAll(array('accountStatus' => 1));
                break;
            case 1 :
                $data = $this->getAll(array('accountStatus' => 1))->toArray();
                break;
            case 2 :
                $dataArray = array(0 => '-- Select --');
                $data = $this->getAll(array('accountStatus' => 1));
                if ($data->count()) {
                    foreach ($data as $row)
                        $dataArray[$row->id] = $row->displayName;
                }
                return $dataArray;
                break;
        }
        return $data;
    }

    public function search($term, $page = 1, $page_limit = 20, $params = false)
    {
        $term = '%' . str_replace(' ', '%', $term) . '%';

        $sql = $this->getSql();
        $select = $sql->select();
        $select->join(array('p' => 'tbl_user_profile'), 'p.userId=' . $this->table . '.id', array('firstName', 'lastName'), 'LEFT');
        $where = new Db\Sql\Where();
        $where
            ->like($this->table . '.username', $term)
            ->or->like($this->table . '.displayName', $term)
            ->addPredicate(new Db\Sql\Predicate\Expression('CONCAT(p.firstName," ",p.lastName) LIKE ' . $this->getAdapter()->getPlatform()->quoteValue($term)), 'OR');

        $select->where(array($where));

        if ($params && count($params)) {
            if (isset($params['roleId'])) {
                $select->join(array('r' => 'tbl_users_roles'), 'r.userId=' . $this->table . '.id', array(), 'LEFT');
            }
            $select->where($params);
        }

        return $this->getPaginated($select, $sql, $page, $page_limit);
    }

    public function save($model)
    {
        $model = (array)$model;
        if (isset($model['data']) && is_array($model['data']))
            $model['data'] = serialize($model['data']);
        return parent::save($model);
    }
}