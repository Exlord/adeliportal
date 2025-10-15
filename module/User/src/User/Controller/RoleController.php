<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;

use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use User\Model\RoleTable;
use Zend\View\Model\JsonModel;

class RoleController extends \System\Controller\BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('role_table');
        $grid->route = 'admin/users/role';
        $this->getRoleTable()->setVisibleRolesSelect($grid->getSelect());
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $name = new Column('roleName', 'Role Name');

        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, $name, $edit, $delete));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('user/role/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $roles = $this->getRoleTable()->getRoleForSelect(false);
        if ($model)
            unset($roles[$model->id]);
        $form = new \User\Form\Role($roles);

        if (is_null($model)) {
            $action = url('admin/users/role/new');
            $roll = new \User\Model\Role();
        } else {
            $action = url('admin/users/role/edit', array('id' => $model->id));
            $roll = $model;
            $form->get('buttons')->remove('submit-new');
        }

        $form->setAction($action);
        $form->bind($roll);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $level = RoleTable::LEVEL_MEMBER;
                    $parent = $this->getRoleTable()->getAll(array('id' => $roll->parentId));
                    if ($parent) {
                        $parent = $parent->current();
                        if ($parent)
                            $level = $parent->level;
                    }
                    $level += 1;
                    $roll->level = $level;

                    $id = $this->getRoleTable()->save($roll);
                    db_log_info("new user role with id:{$id} is created");
                    if (!isset($post['buttons']['submit-new']))
                        return $this->indexAction();
                    else {
                        $roll = new \User\Model\Role();
                        $form->bind($roll);
                    }
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form));
        $this->viewModel->setTemplate('user/role/new');
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('admin/users/role');

        $role = $this->getRoleTable()->get($id);
        if ($role->locked) {
            $this->flashMessenger()->addErrorMessage("This role is locked and cannot be changed !");
            return $this->invalidRequest('admin/users/role');
        }

        $maxLevel = $this->getRoleTable()->getMaxLevel(current_user()->id);
        if ($role->level > $maxLevel)
            return $this->accessDenied();

        return $this->newAction($role);
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $maxLevel = getSM('role_table')->getMaxLevel(current_user()->id);
                $maxLevelId = getSM('role_table')->get($id);
                if ($maxLevelId->level <= $maxLevel && !$maxLevelId->locked) {
                    $this->getServiceLocator()->get('role_table')->remove($id);
                    $this->getServiceLocator()->get('user_role_perm_table')->removeByRoleId($id);
                    return new JsonModel(array('status' => 1));
                } else
                    return new JsonModel(array('status' => 0, 'msg' => t('Access Denied')));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    /**
     * @return \User\Model\RoleTable
     */
    private function getRoleTable()
    {
        return getSM('role_table');
    }
}
