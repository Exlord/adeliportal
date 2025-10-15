<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EducationalCenter\Controller;

use Application\API\App;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Visualizer;
use EducationalCenter\Model\WorkshopAttendanceTable;
use EducationalCenter\Model\WorkshopClassTable;
use EducationalCenter\Module;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use Zend\Db\Sql\Predicate\Expression;
use Zend\View\Model\JsonModel;

class WorkshopClass extends BaseAbstractActionController
{
    private $indexType = 'current-workshop';

    public function indexAction()
    {
        $indexType = $this->indexType;
        $workshop = null;
        if ($this->indexType == 'current-workshop') {
            $workshop = $this->params()->fromRoute('workshop', false);
            if (!$workshop)
                return $this->invalidRequest('admin/educational-center/workshop');
        }

        $grid = new DataGrid('ec_workshop_class_table');
        if ($this->indexType == 'current-workshop') {
            $grid->route = 'admin/educational-center/workshop/class';
            $grid->routeParams['workshop'] = $workshop;
        } else {
            $grid->route = 'admin/educational-center/my-workshop-classes';
        }

        $columns = array();

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);
        $columns[] = $id;

        $title = new Custom('title', 'Title',
            function (Custom $col) {
                return Common::Link(
                    $col->dataRow->title,
                    url('app/workshop/class', array('workshop' => $col->dataRow->workshopId, 'class' => $col->dataRow->id)),
                    array('target' => '_blank')
                );
            },
            array(),
            true
        );
        $title->hasTextFilter = true;
        $columns[] = $title;

        $start_time = new Date('firstSession', 'Start Time', array(), 0, 3);
        $columns[] = $start_time;

        $finish_time = new Date('lastSession', 'End Time', array(), 0, 3);
        $columns[] = $finish_time;

        $capacity = new Custom('capacity', 'Capacity',
            function (Custom $col) {
                $class = 'progress-bar-success';
                $capacity = $col->dataRow->capacity;
                $used = $col->dataRow->usedCapacity;
                if ($used > ($capacity / 3))
                    $class = 'progress-bar-warning';
                if ($used > ($capacity / 3) * 2)
                    $class = 'progress-bar-danger';

                $percent = (int)($used * 100 / $capacity);

                $progress = "<div class='progress' title='{$percent}%'>
                          <div class='progress-bar $class progress-bar-striped' role='progressbar' aria-valuenow='{$used}' aria-valuemin='0' aria-valuemax='{$capacity}' style='width: {$percent}%;min-width:30px;'>
                           {$used}/{$capacity}
                          </div>
                        </div>";

                return $progress;
            },
            array(
                'headerAttr' => array('width' => '100px')
            )
        );
        $columns[] = $capacity;

        if ($this->indexType == 'current-workshop') {
            $educator = new Custom('educatorId', 'Educator', function (Custom $col) {
                return Common::Link(
                    getUserDisplayName($col->dataRow),
                    url('admin/users/view', array('id' => $col->dataRow->educatorId)),
                    array('class' => 'ajax_page_load'));
            });
            $columns[] = $educator;
        }

        if (isAllowed(Module::ATTENDANCE)) {
            $attendance = new Button('Attendance',
                function (Button $col) use ($indexType) {
                    $col->route = 'admin/educational-center/workshop/class/attendance';
                    $col->routeParams['workshop'] = $col->dataRow->workshopId;
                    $col->routeParams['class'] = $col->dataRow->id;
                    $col->icon = 'glyphicon glyphicon-user';
                },
                array(
                    'contentAttr' => array('align' => 'center', 'class' => array('btn-default', 'ajax_page_load')),
                    'attr' => array('align' => 'center'),
                    'headerAttr' => array('width' => '70px', 'align' => 'center'),
                )
            );
            $columns[] = $attendance;
        }

        if (isAllowed(Module::TIMETABLE)) {
            $timeTable = new Custom('timetable', 'Time Table',
                function (Custom $col) {
                    $s = (int)$col->dataRow->status;
                    if ($s == 0 || $s == 1) {
                        return Common::Link(
                            "<span class='glyphicon glyphicon-time text-primary'></span>",
                            url('admin/educational-center/workshop/class/timetable', array('workshop' => $col->dataRow->workshopId, 'class' => $col->dataRow->id)),
                            array('class' => array('ajax_page_load', 'btn', 'btn-default', 'btn-xs'),
                                'title' => t("Click here to change this class time table"))
                        );
                    }
                },
                array(
                    'contentAttr' => array('align' => 'center'), 'attr' => array('align' => 'center'),
                    'headerAttr' => array('width' => '70px', 'align' => 'center'),
                )
            );
            $columns[] = $timeTable;
        }


        $status = new Custom('status', 'Status',
            function (Custom $col) use ($indexType) {
                $s = $col->dataRow->status;
                $title = '';
                $icon = '';

                $stage = $s;
                $now = time();

                switch ($s) {
                    case '0':
                        $title = t('Disabled');
                        $icon = 'glyphicon glyphicon-remove-sign text-danger';
                        break;
                    case '1':
                        $title = t('Enabled') . '/';
                        $icon = 'glyphicon glyphicon-ok text-primary';
                        if (has_value($col->dataRow->firstSession) && has_value($col->dataRow->lastSession)) {
                            if ($col->dataRow->firstSession > $now) {
                                $title .= t('Waiting');
                                $stage = '1.1';

                            } elseif ($col->dataRow->firstSession <= $now && $col->dataRow->lastSession >= $now) {
                                $title = t('Started');
                                $stage = '1.2';
                                $icon = 'glyphicon glyphicon-play text-success';
                            } elseif ($col->dataRow->lastSession < $now) {
                                $title = t('Finished');
                                $stage = '1.3';
                                $icon = 'glyphicon glyphicon-stop';
                            }
                        }
                        break;
                    case '2':
                        $title = t('Canceled');
                        $icon = 'glyphicon glyphicon-ban-circle text-danger';
                        break;
                    case '3':
                        $title = t('Started');
                        $stage = '1.2';
                        $icon = 'glyphicon glyphicon-play text-success';
                        break;
                    case '4':
                        $title = t('Finished');
                        $stage = '1.3';
                        $icon = 'glyphicon glyphicon-stop';
                        break;
                }

                $content = "<span title='{$title}' class='{$icon} grid-icon'></span>";

                if ($indexType == 'current-workshop') {
                    $icon = '';
                    $url = '#';
                    $class = 'btn btn-default btn-xs';
                    $title = '';
                    $route = false;
                    $routeParams = array('id' => $col->dataRow->id, 'workshop' => $col->dataRow->workshopId);

                    switch ($s) {
                        case '0':
                            $title = t('Enable');
                            $icon = 'glyphicon glyphicon-ok text-primary';
                            $route = 'admin/educational-center/workshop/class/change-status';
                            $routeParams['status'] = 1;
                            $class .= ' ajax_page_load';
                            break;
                        case '1':
                            if ($stage != '1.3') {
                                $title = t('Cancel');
                                $icon = 'glyphicon glyphicon-ban-circle text-danger';
                                $route = 'admin/educational-center/workshop/class/change-status';
                                $routeParams['status'] = 2;
                                $class .= ' ajax_page_load';
                            }
                            break;
                    }

                    if ($route) {
                        $url = url($route, $routeParams);
                        $content .= ' ' . Common::Link(
                                "<span class='$icon'></span>", $url,
                                array('class' => $class, 'title' => $title)
                            );
                    }
                }

                return $content;
            },
            array(
                'contentAttr' => array('align' => 'center'), 'attr' => array('align' => 'center'),
                'headerAttr' => array('width' => '70px', 'align' => 'center'),
            ));
        $columns[] = $status;

        if ($this->indexType == 'current-workshop') {
            $edit = new EditButton();
            $columns[] = $edit;

            $del = new DeleteButton();
            $columns[] = $del;
        }

        $grid->addColumns($columns);
        if ($this->indexType == 'current-workshop') {
            $grid->addDeleteSelectedButton();
            $grid->addNewButton('New Class');
        }

        $select = $grid->getSelect();
        $select
            ->columns(
                array('id', 'workshopId', 'educatorId', 'title', 'note', 'capacity', 'price', 'status',
                    'usedCapacity' => new Expression("(SELECT COUNT(id) FROM `tbl_ec_workshop_attendance` WHERE `classId` = `tbl_ec_workshop_class`.`id`)"),
                    'firstSession' => new Expression("(SELECT MIN(`start`) FROM `tbl_ec_workshop_timetable` WHERE `classId` = `tbl_ec_workshop_class`.`id` AND `status` = '0')"),
                    'lastSession' => new Expression("(SELECT MAX(`end`) FROM `tbl_ec_workshop_timetable` WHERE `classId` = `tbl_ec_workshop_class`.`id` AND `status` = '0')")
                ))
            ->join(array('u' => 'tbl_users'), $grid->getTableGateway()->table . '.educatorId=u.id', array('username', 'displayName'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName'), 'LEFT');

        if ($this->indexType == 'current-workshop')
            $select->where(array($grid->getTableGateway()->table . '.workshopId' => $workshop));
        elseif ($this->indexType == 'educator')
            $select->where(array($grid->getTableGateway()->table . '.educatorId' => current_user()->id));

        $grid->defaultSort = $id;
        $grid->defaultSortDirection = 'DESC';

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        $this->viewModel->setTemplate('educational-center/workshop-class/index');
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $workshop = $this->params()->fromRoute('workshop', false);
        if (!$workshop)
            return $this->invalidRequest('admin/educational-center/workshop');

        $educatorUserRoles = array();
        $config = getConfig('educational-center');
        if (isset($config->varValue['educatorUserRole'])) {
            $educatorUserRoles = $config->varValue['educatorUserRole'];
        }

        if (!count($educatorUserRoles)) {
            $this->flashMessenger()->addErrorMessage('Educational center configs has not been initialized yet !');
            return $this->forward()->dispatch('EducationalCenter\Controller\EducationalCenter', array('action' => 'config'));
        }


        $oldEducator = false;
        $form = new \EducationalCenter\Form\WorkshopClass();
        $form->setAttribute('data-cancel', url('admin/educational-center/workshop/class', array('workshop' => $workshop)));
        if (!$model) {
            $model = new \EducationalCenter\Model\WorkshopClass();
            $model->workshopId = $workshop;
            $form->setAction(url('admin/educational-center/workshop/class/new', array('workshop' => $workshop)));
            $form = prepareForm($form, array('submit-close'));
        } else {
            $form->setAction(url('admin/educational-center/workshop/class/edit', array('id' => $model->id, 'workshop' => $workshop)));
            $form = prepareForm($form, array('submit-new'));
            $oldEducator = $model->educatorId;
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {
                    $this->getTable()->save($model);
                    $this->flashMessenger()->addSuccessMessage('New workshop class created successfully');

                    if ($notifyApi = getNotifyApi()) {
                        $class = $this->getTable()->get($model->id)->current();
                        if (has_value($class->email)) {
                            $email = $notifyApi->getEmail();
                            $email->to = array($class->email => getUserDisplayName($class));
                            $email->from = Mail::getFrom();
                            if ($model->isNew)
                                $email->subject = t('New Workshop Class');
                            else
                                $email->subject = t('Workshop Class Modified');
                            $email->entityType = 'EducationalCenterWorkshop';
                            $email->queued = 0;
                        }

                        $internal = $notifyApi->getInternal();
                        $internal->uId = $class->educatorId;

                        $classTitle = $class->title . ' » ' . $class->workshopTitle;
                        $classTitleHtml = "<strong>{$classTitle}</strong>";

                        $notifyApi->notify('EducationalCenter', 'workshop_new_class_educator', array(
                            '__WORKSHOP_CLASS_NAME__' => $classTitle,
                            '__WORKSHOP_CLASS_URL__' => Common::Link(
                                $classTitleHtml,
                                App::siteUrl() . url('app/workshop/class', array('workshop' => $class->workshopId, 'class' => $class->id)),
                                array('target' => '_blank')
                            ),
                        ));


                        if (!$model->isNew) {
                            $attendances = $this->getAttendanceTable()->getAttendances($class->id);

                            $emails = array();
                            $mobiles = array();
                            $uids = array();
                            foreach ($attendances as $att) {
                                if (has_value($attendances->email))
                                    $emails[$attendances->email] = getUserDisplayName($attendances);

                                if (has_value($attendances->mobile))
                                    $mobiles[] = $attendances->mobile;

                                $uids[] = $attendances->userId;
                            }

                            if (count($emails)) {
                                $email = $notifyApi->getEmail();
                                $email->to = $emails;
                                $email->from = Mail::getFrom();
                                $email->subject = t('Workshop Class Modified');
                                $email->entityType = 'EducationalCenterWorkshop';
                                $email->queued = 1;
                            }

                            if (count($mobiles)) {
                                $sms = $notifyApi->getSms();
                                $sms->to = $mobiles;
                            }

                            if (count($uids)) {
                                $notifyApi->getInternal()->uId = $uids;
                            }

                            $notifyApi->notify('EducationalCenter', 'workshop_class_modified', array(
                                '__WORKSHOP_CLASS_NAME__' => $classTitle,
                                '__WORKSHOP_CLASS_URL__' => Common::Link(
                                    $classTitleHtml,
                                    App::siteUrl() . url('app/workshop/class', array('workshop' => $class->workshopId, 'class' => $class->id)),
                                    array('target' => '_blank')
                                ),
                            ));
                        }

                        if ($oldEducator && $oldEducator != $class->educatorId) {
                            $oldEducator = getSM('users_table')->get($oldEducator);

                            $emails = array();
                            if (has_value($oldEducator->email))
                                $emails[$oldEducator->email] = getUserDisplayName($oldEducator);
                            if (has_value($class->email))
                                $emails[$class->email] = getUserDisplayName($class);
                            if (count($emails)) {
                                $email = $notifyApi->getEmail();
                                $email->to = $emails;
                                $email->from = Mail::getFrom();
                                $email->subject = t('Workshop Class Educator Changed');
                                $email->entityType = 'EducationalCenterWorkshop';
                                $email->queued = 0;
                            }

                            $notifyApi->getInternal()->uId = array($oldEducator->id, $class->educatorId);

                            $notifyApi->notify('EducationalCenter', 'workshop_class_educator_changed', array(
                                '__WORKSHOP_CLASS_NAME__' => $classTitle,
                                '__WORKSHOP_CLASS_URL__' => Common::Link(
                                    $classTitleHtml,
                                    App::siteUrl() . url('app/workshop/class', array('workshop' => $class->workshopId, 'class' => $class->id)),
                                    array('target' => '_blank')
                                ),
                                '__WORKSHOP_CLASS_EDUCATOR__' => Common::Link(
                                    getUserDisplayName($oldEducator),
                                    App::siteUrl() . url('app/user/view', array('id' => $oldEducator->id)),
                                    array('target' => '_blank')
                                ),
                                '__WORKSHOP_CLASS_NEW_EDUCATOR__' => Common::Link(
                                    getUserDisplayName($class),
                                    App::siteUrl() . url('app/user/view', array('id' => $class->educatorId)),
                                    array('target' => '_blank')
                                ),
                            ));
                        }
                    }

                    if ($this->isSubmitAndClose())
                        return $this->indexAction(); //TODO if not edit redirect to new time table
                    elseif ($this->isSubmitAndNew()) {
                        $model = new \EducationalCenter\Model\WorkshopClass();
                        $form->bind($model);
                    } elseif ($model->isNew)
                        return $this->indexAction();
                    //TODO if not edit redirect to new time table
                } else
                    $this->formHasErrors();

            } elseif ($this->isCancel()) {
                return $this->indexAction();
            }
        }

        if (!$model->isNew) {
            $model->text = $model->username;
            $name = '';
            if (!empty($model->firstName))
                $name .= $model->firstName;
            if (!empty($model->lastName)) {
                if (!empty($name))
                    $name .= ' ';
                $name .= $model->lastName;
            }
            $model->name = $name;
        }
        $this->viewModel->setTemplate('educational-center/workshop-class/new');
        $this->viewModel->setVariables(array('form' => $form, 'educatorUserRoles' => $educatorUserRoles, 'model' => $model));
        return $this->viewModel;
    }

    public function editAction()
    {
        $workshop = $this->params()->fromRoute('workshop', false);
        if (!$workshop)
            return $this->invalidRequest('admin/educational-center/workshop');

        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model) {
                $model = $model->current();
                $model->isNew = false;
                return $this->newAction($model);
            }
        }
        return $this->invalidRequest('admin/educational-center/workshop/class', array('workshop' => $workshop));
    }

    public function changeStatusAction()
    {
        $status = (int)$this->params()->fromRoute('status', false);
        $id = $this->params()->fromRoute('id', 0);

        if ($id && $status !== false) {
            $workshop = $this->getTable()->getItem($id);
            $allowChange = false;
            if ($workshop->status == '0' && $status == 1) {
                $allowChange = true;
                //TODO notify workshop is enabled
            }
            if ($workshop->status == '1' && $status == 2) {
                $allowChange = true;
                if ($notifyApi = getNotifyApi()) {

                    $class = $this->getTable()->get($id);
                    if ($class && $class = $class->current()) {

                        $classTitle = $class->title . ' » ' . $class->workshopTitle;
                        $classTitleHtml = "<strong>{$classTitle}</strong>";


                        //region EDUCATOR
                        if (has_value($class->email)) {
                            $email = $notifyApi->getEmail();
                            $email->to = array($class->email => getUserDisplayName($class));
                            $email->from = Mail::getFrom();
                            $email->subject = t('Workshop Class Canceled');
                            $email->entityType = 'EducationalCenterWorkshop';
                            $email->queued = 0;
                        }

                        if (has_value($class->mobile)) {
                            $sms = $notifyApi->getSms();
                            $sms->to = $class->mobile;
                        }

                        $internal = $notifyApi->getInternal();
                        $internal->uId = $class->educatorId;

                        $notifyApi->notify('EducationalCenter', 'workshop_class_canceled', array(
                            '__WORKSHOP_CLASS_NAME__' => $classTitle,
                            '__WORKSHOP_CLASS_URL__' => Common::Link(
                                $classTitleHtml,
                                App::siteUrl() . url('app/workshop/class', array('workshop' => $class->workshopId, 'class' => $class->id)),
                                array('target' => '_blank')
                            ),
                        ));
                        //endregion


                        //region ATTENDANCES
                        $attendances = $this->getAttendanceTable()->getAttendances($class->id);
                        $emails = array();
                        $mobiles = array();
                        $uids = array();
                        foreach ($attendances as $att) {
                            if (has_value($att->email))
                                $emails[$att->email] = getUserDisplayName($att);

                            if (has_value($att->mobile))
                                $mobiles[] = $att->mobile;

                            $uids[] = $att->userId;
                        }

                        if (count($emails)) {
                            $email = $notifyApi->getEmail();
                            $email->to = $emails;
                            $email->from = Mail::getFrom();
                            $email->subject = t('Workshop Class Canceled');
                            $email->entityType = 'EducationalCenterWorkshop';
                            $email->queued = 1;
                        }

                        if (count($mobiles)) {
                            $sms = $notifyApi->getSms();
                            $sms->to = $mobiles;
                        }

                        if (count($uids)) {
                            $notifyApi->getInternal()->uId = $uids;
                        }

                        $notifyApi->notify('EducationalCenter', 'workshop_class_canceled', array(
                            '__WORKSHOP_CLASS_NAME__' => $classTitle,
                            '__WORKSHOP_CLASS_URL__' => Common::Link(
                                $classTitleHtml,
                                App::siteUrl() . url('app/workshop/class', array('workshop' => $class->workshopId, 'class' => $class->id)),
                                array('target' => '_blank')
                            ),
                        ));
                        //endregion
                    } else {
                        //we should never get here
                        $this->flashMessenger()->addErrorMessage('Class not found');
                        return $this->indexAction();
                    }
                }
            }
            if ($allowChange) {
                $this->getTable()->update(array('status' => $status), array('id' => $id));
                return $this->indexAction();
            }
        }

        $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
        return $this->indexAction();
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getTable()->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function myWorkshopClassesAction()
    {
        $this->indexType = 'educator';
        return $this->indexAction();
    }

    public function cancelAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        if (!$id)
            return $this->indexAction();
    }

    //region Private Methods
    /**
     * @return WorkshopClassTable
     */
    private function getTable()
    {
        return getSM('ec_workshop_class_table');
    }

    /**
     * @return WorkshopAttendanceTable
     */
    private function getAttendanceTable()
    {
        return getSM('ec_workshop_attendance_table');
    }
    //endregion
}
