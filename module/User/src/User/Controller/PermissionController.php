<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;

use Application\API\App;
use User\Model\RoleTable;
use User\Permissions\Acl\AclManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class PermissionController extends \System\Controller\BaseAbstractActionController
{
    private function recursive(&$items, $parents, $parentId, $indent)
    {
        ++$indent;
        foreach ($parents[$parentId] as $item) {
            $item->indent = $indent;
            $items[$item->getResourceId()] = $item;
            if (isset($parents[$item->getResourceId()])) {
                $this->recursive($items, $parents, $item->getResourceId(), $indent);
            }
        }
    }

    public function indexAction()
    {
        $roles = $this->getRoleTable()->getVisibleRoles(false);

        $permissions = array();
        $resources_list = array();
        $acl = AclManager::load();
        $resources = $acl->getResourceObjects();
        /* @var $res \User\Permissions\Acl\Resource\Resource */
        foreach ($resources as $resourceId => $res) {
            /**
             * @var $instance \User\Permissions\Acl\Resource\Resource
             * @var $parent \User\Permissions\Acl\Resource\Resource
             */
            $instance = $res['instance'];
            $parent = 0;
            if (isset($res['parent'])) {
                $parent = $res['parent'];
                $parent = $parent->getResourceId();
            }
            $resources_list[$parent][] = $instance;
        }

        $this->recursive($permissions, $resources_list, 0, 0);
        $resources_list = $permissions;
        $permissions = array();
        foreach ($resources_list as $key => $res) {
            $permissions[$res->getModule()][$key] = $res;
        }

//        $rollPermission = $this->getServiceLocator()->get('user_role_perm_table')->fetchAll();
//        $rollPerm_array = array();
//        foreach ($rollPermission as $rollP) {
//            $rollPerm_array[] = $rollP->pId . '_' . $rollP->roleId;
//        }
//
//        $permissions = $this->getServiceLocator()->get('user_perm_table')->fetchAll();


//        $perm_array_parent = array();
//        $perm_array_child = array();
//
//        foreach ($permissions as $perm) {
//            if ($perm->parentId == 0)
//                $perm_array_parent[$perm->id] = $perm;
//            else
//                $perm_array_child[$perm->parentId][] = $perm;
//        }

        $permissionsConfig = array();
        $config = getConfig('permissions');
        if (isset($config->varValue['perms']))
            $permissionsConfig = $config->varValue['perms'];

        $this->viewModel->setTemplate('user/permission/index');
        $this->viewModel->setVariables(array(
            'roles' => $roles,
            'permissions' => $permissions,
            'acl' => $acl,
            'permissionsConfig' => $permissionsConfig,
//            'perm_array_parent' => $perm_array_parent,
//            'perm_array_child' => $perm_array_child,
//            'perms' => $permissions,
//            'rollPs' => $rollPerm_array
        ));
        return $this->viewModel;
    }

    public function rebuildAction()
    {
        getCache(true)->removeItem(Acl::ACL_CACHE_ID);
//        if (cacheExist(Acl::ACL_CACHE_ID)) {
//            getCache()->removeItem(Acl::ACL_CACHE_ID);
//        }
        return $this->indexAction();
    }

    public function changeAction()
    {
        $response = array();
//        $response['status'] = 'invalid';
        if ($this->request->isPost()) {
            $post = $this->params()->fromPost();
            //TODO get this rolesId's level $level
            //TODO get current user's max role level $userMaxLevel
            //TODO if($userMaxLevel >= $level)
            $roleId = array_shift(array_keys($post['perms']));
            $resource = array_shift(array_keys($post['perms'][$roleId]));
            $status = $post['perms'][$roleId][$resource];

            $MaxRoleId = getSM('role_table')->get($roleId)->level;
            $currentMaxRoleId = getSM('role_table')->getMaxLevel(current_user()->id);

            if ($currentMaxRoleId >= $MaxRoleId) {

                $config_table = $this->getServiceLocator()->get('config_table');
                $config = getConfig('permissions');
                if (!isset($config->varValue['perms']))
                    $config->varValue['perms'] = array();

                if (!isset($config->varValue['perms'][$roleId]))
                    $config->varValue['perms'][$roleId] = array();

                if ((int)$status == 0)
                    unset($config->varValue['perms'][$roleId][$resource]);
                else
                    $config->varValue['perms'][$roleId][$resource] = $status;

                $permissions = $config->varValue;
                $config_table->save($config);

                $acl = AclManager::reload($permissions);
//                $acl->loadPermissions($permissions);

                //test
//                $response['test'] = $roleId . '--' . $resource . '--' . $permissions['perms'][$roleId][$resource];
                $response['result'] = isAllowed($resource, $roleId);

                //in the preview code when a permission was set to inherit ,
                //i was removing the previews allow and deny for that resource that was triggering a chain event
                //that was resetting all child resources to inherit

//                if ($acl->hasResource($resource)) {
//                    /* @var $resource \User\Permissions\Acl\Resource\Resource */
//                    $resource = $acl->getResource($resource);
//                    $assert = null;
//                    switch ($status) {
//                        case '0':
//                            $acl->removeAllow($roleId, $resource);
//                            $acl->removeDeny($roleId, $resource);
//                            break;
//                        case '1':
//                            $acl->allow($roleId, $resource);
//                            break;
//                        case '2':
//                            $acl->deny($roleId, $resource);
//                            break;
//                    }
//                }
//                $acl->save();

                $roles = $this->getServiceLocator()->get('role_table')->getAllNested();
                $resources = $acl->getResources();
                $perms_json = array();
                foreach ($roles as $id => $role) {
                    foreach ($resources as $key) {
                        $perm_index = $id . '_' . str_replace(array('/', '-', ':'), '', $key);
                        $perms_json[$perm_index] = $acl->isAllowed($id, $key);
                    }
                }

                $response['perms'] = json_encode($perms_json);
            } else {
                $response['status'] = 0;
                $response['msg'] = t("Access denied. You don't have permission to change this permission");
            }
        }
        return new JsonModel($response);
    }

    /**
     * @return RoleTable
     */
    private function getRoleTable()
    {
        return getSM('role_table');
    }
}
