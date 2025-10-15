<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CustomersClub\Controller;

use CustomersClub\Model\PointsTable;
use CustomersClub\Model\PointsTotalTable;
use CustomersClub\Module;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class Point extends BaseAbstractActionController
{
    public function indexAction($userId = null)
    {
        $grid = new DataGrid('points_table');
        $grid->route = 'admin/customers-club/points';

        $columns = array();

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);
        $columns[] = $id;

        if (!$userId) {
            $user = new Custom('userId', 'User', function (Custom $col) {
                return Common::Link(
                    getUserDisplayName($col->dataRow),
                    url('app/user/user-profile', array('id' => $col->dataRow->userId)),
                    array('target' => '_blank')
                );
            });
            $columns[] = $user;
        }

        $point = new Custom('points', 'Points',
            function (Custom $col) {
                $class = 'label-success';
                $point = (int)$col->dataRow->points;
                if ($point < 0)
                    $class = 'label-danger';
                else
                    $point = '+' . $point;

                return "<span class='label {$class}' dir='ltr'>{$point}</span>";
            },
            array(
                'headerAttr' => array('width' => '70px', 'align' => 'center'),
                'attr' => array('align' => 'center')
            )
        );
        $columns[] = $point;

        $note = new Column('note', 'Note');
        $columns[] = $note;

        $date = new Date('date', 'Date', array(), 0, 3);
        $columns[] = $date;

        if (!$userId) {
            if (isAllowed(Module::DELETE_POINT)) {
                $del = new DeleteButton();
                $columns[] = $del;
            }

            if (isAllowed(Module::EDIT_POINT)) {
                $edit = new EditButton();
                $columns[] = $edit;
            }
        }

        $grid->addColumns($columns);

        if (!$userId) {
            if (isAllowed(Module::DELETE_POINT))
                $grid->addDeleteSelectedButton();

            if (isAllowed(Module::NEW_POINT))
                $grid->addNewButton('Add Point');
        }

        $select = $grid->getSelect();
        $select
            ->join(array('u' => 'tbl_users'), 'tbl_cc_points.userId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT');

        if ($userId)
            $select->where(array('tbl_cc_points.userId' => $userId));

        $grid->defaultSort = $id;
        $grid->defaultSortDirection = 'DESC';

        $myPoints = null;
        if ($userId)
            $myPoints = (int)getSM('points_total_table')->getMyPoints($userId);

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
                'myPoints' => $myPoints
            ));
        $this->viewModel->setTemplate('customers-club/point/index');
        return $this->viewModel;
    }

    public function newAction($model = null, $pointBefore = null)
    {
        $form = new \CustomersClub\Form\Point();
        if (!$model) {
            $model = new \CustomersClub\Model\Point();
            $model->date = time();
            $form->setAction(url('admin/customers-club/points/new'));
        } else {
            $form->setAction(url('admin/customers-club/points/edit', array('id' => $model->id)));
            $form = prepareForm($form, array('submit-new'));
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {
                    $userId = (int)$model->userId;
                    if (!$userId) {
                        $form->get('userId')->setMessages(array(
                            'please select a user'
                        ));
                        $this->formHasErrors();
                    } else {
                        $model->points = $model->type . $model->points;

                        $this->getTable()->save($model, $pointBefore);

                        $this->flashMessenger()->addSuccessMessage('points modification saved successfully.');
                        if ($this->isSubmitAndClose())
                            return $this->indexAction();
                        elseif ($this->isSubmitAndNew()) {
                            $model = new \CustomersClub\Model\Point();
                            $form->bind($model);
                        }
                    }
                } else
                    $this->formHasErrors();

            } elseif ($this->isCancel()) {
                return $this->indexAction();
            }
        }

        $this->viewModel->setTemplate('customers-club/point/new');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model) {
                $pointBefore = $model->points;
                if ((int)$model->points > 0)
                    $model->type = '+';
                else {
                    $model->type = '-';
                    $model->points *= -1;
                }
                return $this->newAction($model, $pointBefore);
            }
        }
        return $this->invalidRequest('admin/customers-club/points');
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $points = $this->getTable()->getAll(array('id' => $id));

                if ($points) {
                    $updates = array();
                    $points_api = getSM('points_api');
                    foreach ($points as $model) {
                        $points_api->notify($model->userId, $model->points, $model->note, 'delete', $model->points);
                        if (!isset($updates[$model->userId]))
                            $updates[$model->userId] = 0;

                        $updates[$model->userId] -= $model->points;
                    }
                    if (count($updates)) {
                        foreach ($updates as $userId => $points)
                            $this->getTotalTable()->save($userId, $points);
                    }
                }
                $this->getTable()->remove($id);
                return new JsonModel(array('status' => 1, 'cmd' => 'Notifications.update("all")'));
            }
        }
        return $this->unknownAjaxError();
    }

    public function myPointsAction()
    {
        return $this->indexAction(current_user()->id);
    }

    /**
     * @return PointsTable
     */
    private function getTable()
    {
        return getSM('points_table');
    }

    /**
     * @return PointsTotalTable
     */
    private function getTotalTable()
    {
        return getSM('points_total_table');
    }
}
