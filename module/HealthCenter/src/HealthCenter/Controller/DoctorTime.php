<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace HealthCenter\Controller;

use Application\API\App;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use DataView\Lib\Visualizer;
use HealthCenter\Model\DoctorTimeTable;
use HealthCenter\Module;
use Localization\API\Date;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use Zend\View\Model\JsonModel;

class DoctorTime extends BaseAbstractActionController
{
    public function indexAction()
    {
        $doctor = $this->params()->fromRoute('doctor', false);
        if (!$doctor) {
            return $this->invalidRequest('admin/health-center/doctors');
        }
        $doctorUser = getSM('user-table')->get($doctor);

        $grid = new DataGrid('hc_doctor_time_table');
        $grid->route = 'admin/health-center/doctors/timetable';
        $grid->routeParams['doctor'] = $doctor;
        $grid->routeParams['id'] = $doctor;

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $date = new \DataView\Lib\Date('start', 'Date');
        $date->sortable = true;

        $start = new \DataView\Lib\Date('start', 'Start Time', array(), -1, 3);
        $start->sortable = true;

        $end = new \DataView\Lib\Date('end', 'End Time', array(), -1, 3);
        $end->sortable = true;

//        $sessionTime = new Custom('date', 'Session Length',
//            function (Custom $col) {
//                $diff = $col->dataRow->end - $col->dataRow->start;
//                return Date::formatInterval($diff);
//            }
//        );

//        $timeDistance = new Custom('date', 'Time Distance',
//            function (Custom $col) {
//                $diff = $col->dataRow->start - time();
//
//                $col->attr['class']['color'] = 'text-success';
//                $ago = '';
//                if ($diff < 0) {
//                    $ago .= ' ' . t('LOCALIZATION_TIME_AGO');
//                    $diff *= -1;
//                    $col->attr['class']['color'] = 'text-danger';
//                }
//
//                $content = Date::formatInterval($diff);
//                return $content . $ago;
//            }
//        );

        $now = time();

        $status = new Custom('status', 'Status',
            function (Custom $col) use ($doctor, $now) {
                $s = $col->dataRow->status;
                $title = '';
                $icon = '';
                if ($s == '0') {
                    if ($col->dataRow->start > $now) {
                        $title = t('HC_TIMETABLE_WAITING');
                        $icon = 'glyphicon glyphicon-question-sign text-info';
                    } elseif ($col->dataRow->start <= $now && $col->dataRow->end >= $now) {
                        $title = t('HC_DOCTOR_TIME_IN_PROGRESS');
                        $icon = 'glyphicon glyphicon-time text-primary';
                    } else {
                        $title = t('HC_DOCTOR_TIME_DONE');
                        $icon = 'glyphicon glyphicon-ok-sign text-success';
                    }
                } elseif ($s == '1') {
                    $title = t('HC_DOCTOR_TIME_CANCELED');

                    $icon = 'glyphicon glyphicon-minus-sign text-danger';
                }

                $content = "<span title='{$title}' class='{$icon} grid-icon'></span>";

                if (isAllowed(Module::TIMETABLE_CHANGE_ALL) || $col->dataRow->doctorId == current_user()->id) {
                    $icon = '';
                    $url = '#';
                    $class = 'btn btn-default btn-xs';
                    $title = '';
                    $route = false;
                    $routeParams = array('id' => $col->dataRow->id, 'doctor' => $doctor);
                    if ($s == '0' && $col->dataRow->start > $now) {
                        $title = t('HC_DOCTOR_TIME_CANCEL');
                        $icon = 'glyphicon glyphicon-ban-circle text-danger';
                        $route = 'admin/health-center/doctors/timetable/change-status';
                        $routeParams['status'] = 1;
                        $class .= ' ajax_page_load';
                    } elseif ($s == '1' && $col->dataRow->start > $now) {
                        $title = t('HC_DOCTOR_TIME_UNCANCEL');
                        $icon = 'glyphicon glyphicon-ok-circle text-success';
                        $route = 'admin/health-center/doctors/timetable/change-status';
                        $routeParams['status'] = 0;
                        $class .= ' ajax_page_load';
                    } else {
                        if ($col->dataRow->start <= $now && $col->dataRow->end >= $now) {
                            $title = t('Visit is in progress and cannot be changed.');
                            $icon = 'glyphicon glyphicon-time text-primary';
                            $url = 'javascript:System.AjaxMessage("' . $title . '")';
                        } elseif ($col->dataRow->end < $now) {
                            $title = t('Visit time has finished and cannot be changed.');
                            $icon = 'glyphicon glyphicon-info-sign text-muted';
                            $url = 'javascript:System.AjaxMessage("' . $title . '")';
                        } elseif (
                            ($s == '0' && $col->dataRow->start < $now) ||
                            ($s == '1' && $col->dataRow->start < $now)
                        ) {
                            $title = t('The time allowed for changing the visit time has passed and this session cannot be changed anymore.');
                            $icon = 'glyphicon glyphicon-info-sign text-info';
                            $url = 'javascript:System.AjaxMessage("' . $title . '")';
                        }
                    }
                    if ($route)
                        $url = url($route, $routeParams);
                    if ($icon) {
                        $content .= ' ' . Common::Link(
                                "<span class='$icon'></span>", $url,
                                array('class' => $class, 'title' => $title)
                            );
                    }
                }

                return $content;
            },
            array(
                'headerAttr' => array('align' => 'center', 'class' => array('merge')),
            )
        );

        $del = new DeleteButton();

        $grid->addColumns(array($id, $date, $start, $end, $status, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('New Time');

        $grid->getSelect()
            ->where(array($grid->getTableGateway()->table . '.doctorId' => $doctor));
        $grid->defaultSort = $date;
        $grid->defaultSortDirection = 'DESC';

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
                'doctorUser' => $doctorUser
            ));
        $this->viewModel->setTemplate('health-center/doctor-time/index');
        return $this->viewModel;
    }

    public function newAction()
    {
        $doctor = $this->params()->fromRoute('doctor', false);
        if (!$doctor) {
            return $this->invalidRequest('admin/health-center/doctors');
        }
        $doctorUser = getSM('user-table')->get($doctor);

        $type = $this->params()->fromRoute('type', false);
        if (!$type) {
            $this->viewModel->setTemplate('health-center/doctor-time/new-type');
            $this->viewModel->setVariables(array('doctor' => $doctor, 'doctorUser' => $doctorUser));
            return $this->viewModel;
        }

        $form = new \HealthCenter\Form\DoctorTime($type);

        $form->setAction(url('admin/health-center/doctors/timetable/new', array('doctor' => $doctor, 'type' => $type)));
        $form->setAttribute('data-cancel', url('admin/health-center/doctors/timetable', array('doctor' => $doctor,)));

        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {

                    $dates = array();
                    $data = $form->getData();

                    $isValid = true;
                    if ($type == 'single-day') {
                        $date = $this->_validateDate($data['date']);
                        if (!$date) {
                            $isValid = false;
                            $form->get('date')->setMessages(array(
                                'The selected date format is invalid'
                            ));
                        } else {
                            if (SYSTEM_LANG == 'fa')
                                $date = Date::getCalendar()->jalali_to_gregorian($date[0], $date[1], $date[2]);

                            $y = $date[0];
                            $mo = $date[1];
                            $d = $date[2];

                            $date = mktime(0, 0, 0, $mo, $d, $y);

                            $h = $post['start']['hour'];
                            if ($post['start']['part'] == 'pm')
                                $h += 12;
                            $m = $post['start']['minute'];
                            $start_time = mktime($h, $m, 0, $mo, $d, $y);

                            $h = $post['end']['hour'];
                            if ($post['end']['part'] == 'pm')
                                $h += 12;
                            $m = $post['end']['minute'];
                            $end_time = mktime($h, $m, 0, $mo, $d, $y);

                            if ($end_time <= $start_time) {
                                $isValid = false;
                                $this->flashMessenger()->addErrorMessage('End Time should be greater than the start time');
                            } else {
                                $dates[] = array(
                                    'start' => $start_time,
                                    'end' => $end_time,
                                    'date' => $date
                                );
                            }
                        }
                    } elseif ($type == 'periodic') {
                        $start_date = $this->_validateDate($post['date-start']);
                        if (!$start_date) {
                            $isValid = false;
                            $form->get('date-start')->setMessages(array('The selected date format is invalid'));
                        }

                        $end_date = $this->_validateDate($post['date-end']);
                        if (!$end_date) {
                            $isValid = false;
                            $form->get('date-end')->setMessages(array('The selected date format is invalid'));
                        }

                        if ($isValid) {

                            //region end_date
                            if (SYSTEM_LANG == 'fa')
                                $end_date = Date::getCalendar()->jalali_to_gregorian($end_date[0], $end_date[1], $end_date[2]);

                            $y = $end_date[0];
                            $mo = $end_date[1];
                            $d = $end_date[2];

                            $end_date = mktime(0, 0, 0, $mo, $d, $y);
                            //endregion

                            //region start_date
                            if (SYSTEM_LANG == 'fa')
                                $start_date = Date::getCalendar()->jalali_to_gregorian($start_date[0], $start_date[1], $start_date[2]);

                            $y = $start_date[0];
                            $mo = $start_date[1];
                            $d = $start_date[2];

                            $current_date = $start_date = mktime(0, 0, 0, $mo, $d, $y);
                            //endregion

                            $days = $post['days'];

                            //region start time
                            $start_h = $post['start']['hour'];
                            if ($post['start']['part'] == 'pm')
                                $start_h += 12;
                            $start_m = $post['start']['minute'];
                            //endregion

                            //region end time
                            $end_h = $post['end']['hour'];
                            if ($post['end']['part'] == 'pm')
                                $end_h += 12;
                            $end_m = $post['end']['minute'];
                            //endregion

                            while ($current_date <= $end_date) {
                                $currentDay = date('N', $current_date);
                                if (in_array($currentDay, $days)) {
                                    $start_time = strtotime('+' . $start_h . ' hours +' . $start_m . ' minutes', $current_date);
                                    $end_time = strtotime('+' . $end_h . ' hours +' . $end_m . ' minutes', $current_date);

                                    if ($end_time <= $start_time) {
                                        $isValid = false;
                                        $this->flashMessenger()->addErrorMessage('End Time should be greater than the start time');
                                        break;
                                    } else {
                                        $dates[] = array(
                                            'start' => $start_time,
                                            'end' => $end_time,
                                            'date' => $current_date
                                        );
                                    }
                                }
                                $current_date = strtotime('+1 day', $current_date);
                            }
                        }

                    } else {
                        return $this->invalidRequest('admin/health-center/workshop');
                    }

                    if (count($dates)) {
                        $savedCount = 0;
                        $patientCount = (int)$post['patientCount'];
                        $patientCount = $patientCount ? $patientCount : 1;
                        $allDates = array();
                        foreach ($dates as $model) {
                            $length = $model['end'] - $model['start'];
                            $eachSession = $length / $patientCount;
                            $start = $model['start'];
                            for ($i = $patientCount; $i > 0; $i--) {
                                $end = (int)($start + $eachSession);
                                $allDates[] = array(
                                    'start' => $start,
                                    'end' => $end,
                                    'status' => 0,
                                    'doctorId' => $doctor,
                                    'date' => $model['date']
                                );
                                $start = $end;
                            }
                        }
                        $savedCount = $this->getTable()->multiSave($allDates);

                        if ($savedCount['effectedRows']) {
                            $this->flashMessenger()->addSuccessMessage(sprintf(t('%s doctor/counselor time table entry created successfully'), $savedCount['effectedRows']));
//                            $this->notify($this->class);
                            //TODO notify
                        }
                    } else
                        $this->flashMessenger()->addErrorMessage('No time table entry wes created !');

                    if ($this->isSubmitAndClose() && $isValid)
                        return $this->indexAction();
                } else
                    $this->formHasErrors();

            } elseif ($this->isCancel()) {
                return $this->indexAction();
            }
        }

        $this->viewModel->setTemplate('health-center/doctor-time/new');
        $this->viewModel->setVariables(array('form' => $form, 'doctorUser' => $doctorUser));
        return $this->viewModel;
    }

    public function changeStatusAction()
    {
        $doctor = $this->params()->fromRoute('doctor', false);
        $id = $this->params()->fromRoute('id', false);
        $status = $this->params()->fromRoute('status', false);

        $msg = t('Invalid Request !');

        if ($doctor && $id && $status !== false) {

            $now = time();

            $col = $this->getTable()->get($id);

            $process = false;

            if (!isAllowed(Module::TIMETABLE_CHANGE_ALL) && $col->dataRow->doctorId != current_user()->id)
                $msg = t('Permission Denied !');
            //if status is normal and there is time to cancel and request is for cancel
            elseif ($col->status == '0' && $col->start > $now && $status == '1') {
                $this->flashMessenger()->addSuccessMessage(t('Selected visit time Canceled'));
                $process = true;
            } //if status is canceled and there is time to uncancel and request is for uncancel
            elseif ($col->status == '1' && $col->start > $now && $status == '0') {
                $this->flashMessenger()->addSuccessMessage(t('Selected visit time  Uncanceled'));
                $process = true;
            }

            if ($process) {
                $this->getTable()->update(array('status' => $status), array('id' => $id));
//                $this->notify($col->classId);
                //TODO notify
                $msg = false;
            }
        }
        if ($msg)
            $this->flashMessenger()->addErrorMessage($msg);

        return $this->indexAction();
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $time = $this->getTable()->get($id);
                $this->getTable()->remove($id); //TODO check if this is not reserved
                //TODO notify
//                if ($time)
//                    $this->notify($time->doctorId);
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    //region Private Methods

    private function _validateDate($date)
    {
        $date = explode('/', $date);
        if (count($date) == 3)
            return $date;
        else
            return false;
    }

//    private function notify($class)
//    {
//        if ($notifyApi = getNotifyApi()) {
//
//            $class = $this->getClassTable()->get($class)->current();
//
//            $classTitle = $class->title . ' » ' . $class->workshopTitle;
//            $classTitleHtml = "<strong>{$classTitle}</strong>";
//
//            //------------------------------ NOTIFY EDUCATOR
//            if (has_value($class->email)) {
//                $email = $notifyApi->getEmail();
//                $email->to = array($class->email => getUserDisplayName($class));
//                $email->from = Mail::getFrom();
//                $email->subject = t('Workshop Class Timetable Modified');
//                $email->entityType = 'HealthCenterWorkshop';
//                $email->queued = 0;
//            }
//
//            $internal = $notifyApi->getInternal();
//            $internal->uId = $class->educatorId;
//
//            $notifyApi->notify('HealthCenter', 'workshop_class_timetable_changed', array(
//                '__WORKSHOP_CLASS_NAME__' => $classTitle,
//                '__WORKSHOP_CLASS_URL__' => Common::Link(
//                        $classTitleHtml,
//                        App::siteUrl() . url('app/workshop/class', array('workshop' => $class->workshopId, 'class' => $class->id)),
//                        array('target' => '_blank')
//                    ),
//            ));
//
//            //------------------------------ NOTIFY ATTENDANCES
//            $attendances = $this->getAttendanceTable()->getAttendances($class->id);
//            $emails = array();
//            $mobiles = array();
//            $uids = array();
//            foreach ($attendances as $att) {
//                if (has_value($attendances->email))
//                    $emails[$attendances->email] = getUserDisplayName($attendances);
//
//                if (has_value($attendances->mobile))
//                    $mobiles[] = $attendances->mobile;
//
//                $uids[] = $attendances->userId;
//            }
//
//            if (count($emails)) {
//                $email = $notifyApi->getEmail();
//                $email->to = $emails;
//                $email->from = Mail::getFrom();
//                $email->subject = t('Workshop Class Timetable Modified');
//                $email->entityType = 'HealthCenterWorkshop';
//                $email->queued = 1;
//            }
//
//            if (count($mobiles)) {
//                $sms = $notifyApi->getSms();
//                $sms->to = $mobiles;
//            }
//
//            if (count($uids)) {
//                $notifyApi->getInternal()->uId = $uids;
//            }
//
//            $notifyApi->notify('HealthCenter', 'workshop_class_timetable_changed', array(
//                '__WORKSHOP_CLASS_NAME__' => $classTitle,
//                '__WORKSHOP_CLASS_URL__' => Common::Link(
//                        $classTitleHtml,
//                        App::siteUrl() . url('app/workshop/class', array('workshop' => $class->workshopId, 'class' => $class->id)),
//                        array('target' => '_blank')
//                    ),
//            ));
//        }
//    }

    /**
     * @return DoctorTimeTable
     */
    private function getTable()
    {
        return getSM('hc_doctor_timetable');
    }
    //endregion
}
