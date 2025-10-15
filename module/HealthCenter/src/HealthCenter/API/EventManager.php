<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/7/2014
 * Time: 3:12 PM
 */

namespace HealthCenter\API;


use Application\API\Widgets;
use Application\Model\Config;
use Cron\API\Cron;
use HealthCenter\Model\DoctorReservationTable;
use HealthCenter\Module;
use Note\API\Note;
use System\Controller\BaseAbstractActionController;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\Mvc\Controller\AbstractActionController;

class EventManager
{
    public function onNoteEventVisibility(Event $e){
        $entityType = $e->getParam('entityType');
        if ($entityType == 'patient_profile') {
            Note::$visibility['patient'] = 'NOTE_VISIBILITY_PATIENT';
            Note::$visibility['doctors'] = 'NOTE_VISIBILITY_DOCTORS';
        }
    }

    public function onNoteEventVisibilityFilter(Event $e){
        $entityType = $e->getParam('entityType');
        if ($entityType == 'patient_profile') {

            $entityId = $e->getParam('entityId');
            /* @var $where Where */
            $where = $e->getParam('where');
            /* @var $select Select */
            $select = $e->getParam('select');

            //i am a patient, let me see the notes that has patient level visibility
            if (current_user()->id == $entityId)
                $where->or->equalTo('v.visibility', 'patient');

            //i am a doctor
            if (HC::IsDoctor()) {

                $select2 = new Select('tbl_hc_doctor_reservation');
                $select2
                    ->columns(array('doctorId'))
                    ->where(array(
                        //get this patients doctors
                        'userId' => new Expression('tbl_note.entityId'),
                        //i have visited this patient before
                        'status' => 5
                    ));

                $myWhere = new Where();
                //the notes that have doctors visibility
                $myWhere->equalTo('v.visibility', 'doctors');
                //i am one of this patients doctors
                $myWhere->in(current_user()->id, $select2);

                $where->or->addPredicate($myWhere);
            }
        }
    }

    public function onCC_CustomerRecords_Load(Event $e)
    {
        $ccApi = $e->getTarget();
        $userId = $e->getParam('userId');
        $data = getSM('hc_doctor_reservation')->getReserveCount($userId);


        if ($data && $data->count()) {
            foreach ($data as $rows) {
                $row = array();
                $class = '';
                switch ($rows['status']) {
                    case '0':
                        break;
                    case '1':
                    case '5':
                        $class = 'success';
                        break;
                    case '2':
                    case '3':
                    case '4':
                        $class = 'danger';
                        break;
                }
                $row['class'] = array($class);
                $row['data'] = array(t(DoctorReservationTable::$ReserveCodes[$rows['status']]), $rows['count']);
                $ccApi->records['Health Center Reservations'][] = $row;
            }
        }
    }

    public function onUserDelete(Event $e)
    {
        $userId = $e->getParam('userId', false);
        if ($userId) {
            //delete times for this user
            getSM('hc_doctor_time_table')->removeByDoctor($userId);
            getSM('hc_doctor_profile_table')->remove($userId);
            getSM('hc_doctor_ref_table')->removeByDoctor($userId);
            getSM('hc_doctor_reservation')->removeByDoctor($userId);
        }
    }

    public function onDashboardLoad(Event $e)
    {
        /* @var $widget Widgets */
        $widget = $e->getTarget();

        if (isAllowed(Module::DOCTOR_PANEL)) {
            $data = $widget->getAction('HealthCenter\Controller\Doctor', 'panel');
            $widget->data[] = $widget->wrap($data, 'col-md-6');
        }

        if (isAllowed(Module::PATIENT_PANEL)) {
            $data = $widget->getAction('HealthCenter\Controller\Patient', 'profile');
            $widget->data[] = $widget->wrap($data);
        }
    }

    public function onCronRun(Config $last_run)
    {
        $start = microtime(true);
        $interval = '+ 1 minute';
        $last = @$last_run->varValue['HealthCenter_last_run'];

        if (Cron::ShouldRun($interval, $last)) {

            $reservationTable = getSM('hc_doctor_reservation');

            //region Update registration status to failed for those how have not payed in time
            $reservationTable->setPaymentFailed();
            //endregion

//            db_log_info(sprintf(t('Sitemap generated in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));

            $last_run->varValue['HealthCenter_last_run'] = time();
        }
    }
} 