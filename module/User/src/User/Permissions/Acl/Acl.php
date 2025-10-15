<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 5/20/13
 * Time: 4:30 PM
 */

namespace User\Permissions\Acl;

use Application\API\App;
use Zend\EventManager\EventManager;
use User\Permissions\Acl\Resource\Resource;

/**
 * Class Acl
 * @package User\Permissions\Acl
 */
class Acl extends \Zend\Permissions\Acl\Acl
{
    const ACL_CACHE_ID = 'acl_full_object';
//    const ACL_LOADING = 'acl_loading';

    /**
     * @return Acl
     */
    public function init($permissions = null)
    {
        //load acl roles
        $roles = getSM()->get('role_table')->getAllNested();
        foreach ($roles as $role) {
            $parent = null;
            if ($role->parentId)
                $parent = $role->parentId;
            $this->addRole($role->id, $parent);
        }

        $this->addResource(new Resource('route:app', 'Application', 'Application'));
        //load acl resources
        /* @var $mm \Zend\ModuleManager\ModuleManager */
        $mm = getSM()->get('ModuleManager');
        foreach ($mm->getModules() as $module) {
            $class = "\\$module\\API\\ACL";
            if (class_exists($class)) {
                $acl = $class::load();
                $this->makeAcl($acl, $module);
            }
        }

        $this->loadPermissions($permissions);
    }

    public function loadPermissions($config = null)
    {
        //load permissions
        if (!$config)
            $config = getConfig('permissions')->varValue;

        $this->allow('1', 'route:app');
        $this->allow('2', 'route:app');

        if (isset($config['perms'])) {
            foreach ($config['perms'] as $roleId => $perms) {
                if ($this->hasRole($roleId)) {
                    foreach ($perms as $resource => $status) {
                        if ($this->hasResource($resource)) {
                            /* @var $resource \User\Permissions\Acl\Resource\Resource */
                            $resource = $this->getResource($resource);
                            $assert = null;
                            switch ($status) {
                                //inherit from parent
                                case '0':
//                                    $this->removeAllow($roleId, $resource);
//                                    $this->removeDeny($roleId, $resource);
                                    break;
                                //allow
                                case '1':
                                    $this->allow($roleId, $resource);
                                    break;
                                //deny
                                case '2':
                                    $this->deny($roleId, $resource);
                                    break;
                            }
                        }
                    }
                }
            }
        }

        $this->save();
    }

    public function save()
    {
        getCache(true)->setItem(self::ACL_CACHE_ID, $this);
//        setCacheItem(self::ACL_CACHE_ID, $this);
    }

    public function isAllowed($roles = null, $resource = null, $privilege = null)
    {
        //ignore user 1
        if ((int)current_user()->id === 1 && !$roles)
            return true;

        //if resource is not defined ignore it
        if (!$this->hasResource($resource))
            return true;

        //there is only one role
        if ($roles && !is_array($roles)) {
            //role doesn't exist
            if (!$this->hasRole($roles))
                return false;
            return parent::isAllowed($roles, $resource, $privilege);
        }

        $allowed = true;
        if ($this->hasResource($resource)) {
            $allowed = false;
            if (!$roles)
                $roles = current_user()->roles;
            foreach ($roles as $role) {
                if ($this->hasRole($role['id']) && parent::isAllowed($role['id'], $resource))
                    return true;
            }
        }
        return $allowed;
    }

    public function getResourceObjects()
    {
        return $this->resources;
    }

    /**
     * @param $data
     * @param $namespace
     * @param null $parent
     */
    private function makeAcl($data, $namespace, $parent = null)
    {
        if (is_array($data) && count($data)) {
            foreach ($data as $val) {
                if (isset($val['label']) && $val['route']) {
                    $resource = new Resource($val['route'], $val['label'], $namespace);
                    if (isset($val['note']))
                        $resource->setNote($val['note']);

                    $_parent = $parent;
                    if ($parent == null && $val['route'] != 'route:admin' && $val['route'] != 'route:app')
                        $_parent = 'route:app';

                    $this->addResource($resource, $_parent);
                }
                if (isset($val['child_route']) && isset($val['route'])) {
                    $this->makeAcl($val['child_route'], $namespace, $val['route']);
                }
            }
        }
    }
}