<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/7/2014
 * Time: 3:16 PM
 */

namespace EducationalCenter\API;


use Application\API\App;
use Application\API\Widgets;
use Application\Model\Config;
use Cron\API\Cron;
use EducationalCenter\Model\WorkshopAttendanceTable;
use EducationalCenter\Model\WorkshopClassTable;
use EducationalCenter\Module;
use Mail\API\Mail;
use Theme\API\Common;
use Zend\EventManager\Event;

class EventManager
{
    public function onCC_CustomerRecords_Load(Event $e)
    {
        $ccApi = $e->getTarget();
        $userId = $e->getParam('userId');
        $data = getSM('ec_workshop_attendance_table')->getUserRegisterCount($userId);


        if ($data && $data->count()) {
            foreach ($data as $rows) {
                if ($rows['count'] != 0) {
                    $row = array();
                    $class = '';
                    switch ($rows['status']) {
                        case '0':
                            break;
                        case '1':
                            $class = 'success';
                            break;
                        case '2':
                        case '3':
                        case '4':
                            $class = 'danger';
                            break;
                    }
                    $row['class'] = array($class);
                    $row['data'] = array(t(WorkshopAttendanceTable::$RegisterStatus[$rows['status']]), $rows['count']);
                    $ccApi->records['Workshop Registrations'][] = $row;
                }
            }
        }
    }

    public function onUserDelete(Event $e)
    {
        $userId = $e->getParam('userId', false);
        if ($userId) {
        }
    }

    public function onDashboardLoad(Event $e)
    {
        /* @var $widget Widgets */
        $widget = $e->getTarget();

        if (isAllowed(Module::USER_WORKSHOP_CLASSES)) {
            $data = $widget->getAction('EducationalCenter\Controller\WorkshopAttendance', 'my-workshop-classes');
            $widget->data[] = $widget->wrap($data, 'col-md-6');
        }
    }

    public function onCronRun(Config $last_run)
    {
        $start = microtime(true);
        $interval = '+ 1 minute';
        $last = @$last_run->varValue['EducationalCenter_last_run'];
        if (Cron::ShouldRun($interval, $last)) {
            $attendanceTable = getSM('ec_workshop_attendance_table');

            //region Update registration status to failed for those how have not payed in time
            $attendanceTable->setPaymentFailed();
            //endregion
        }
        $interval = '+ 24 hours';//run this only once a day
        if (Cron::ShouldRun($interval, $last)) {

            $hour = date('G');
            //run this only between these hours
            if ($hour <= 18 && $hour >= 15) {
                //region | notify attendance with before first session start | set class status to started
                $classTable = getSM('ec_workshop_class_table');
                $classData = $classTable->getClassesForCronUpdate();
                if ($classData && $classData->count()) {

                    $config = getConfig('educational-center')->varValue;
                    if (isset($config['notifyTime']))
                        $notifyTime = $config['notifyTime'];
                    else
                        $notifyTime = WorkshopClassTable::CLASS_START_NOTIFY_TIME;

                    $now = time();
                    $notifyTime = $now + ($notifyTime * 3600);
                    $halfHour = 1800;

                    $startedClasses = array();
                    $finishedClasses = array();
                    foreach ($classData as $classRow) {

                        //class is not started or its about to be
                        if ($classRow->status == '1') {
                            //Class Not Started Yet
                            if ($classRow->firstSession < $notifyTime + $halfHour && $classRow->firstSession >= $notifyTime - $halfHour) {
                                $attendances = $attendanceTable->getAttendances($classRow->id, 1);

                                $classTitle = $classRow->title . ' » ' . $classRow->workshopTitle;
                                $classTitleHtml = "<strong>{$classTitle}</strong>";

                                if ($notifyApi = getNotifyApi()) {
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
                                        $email->subject = t('Workshop class start time');
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

                                    $notifyApi->notify('EducationalCenter', 'workshop_class_before_start', array(
                                        '__WORKSHOP_CLASS_NAME__' => $classTitle,
                                        '__WORKSHOP_CLASS_URL__' => Common::Link(
                                            $classTitleHtml,
                                            App::siteUrl() . url('app/workshop/class', array('workshop' => $classRow->workshopId, 'class' => $classRow->id)),
                                            array('target' => '_blank')
                                        ),
                                        '__WORKSHOP_CLASS_START_TIME__' => dateFormat($classRow->firstSession, 0, 3)
                                    ));
                                }
                            } elseif ($classRow->firstSession >= $now && $classData->lastSession < $now) { //Class Started
                                $startedClasses[] = $classRow->id;
                            }


                        } elseif ($classRow->status == '3') { //class is started

                            //class is finished
                            if ($classRow->lastSession < $now) {
                                $finishedClasses[] = $classRow->id;
                            }
                        }
                    }

                    //set class status to started
                    if (count($startedClasses)) {
                        $classTable->update(array('status' => 3), array('id' => $startedClasses));
                    }

                    //set class status to finished
                    if (count($finishedClasses)) {
                        $classTable->update(array('status' => 4), array('id' => $finishedClasses));
                    }
                }
            }
            //endregion

//            db_log_info(sprintf(t('Sitemap generated in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));

            $last_run->varValue['EducationalCenter_last_run'] = time();
        }
    }
} 