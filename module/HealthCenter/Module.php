<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace HealthCenter;

use Cron\API\Cron;
use HealthCenter\API\HC;
use HealthCenter\Model\DoctorReservationTable;
use Note\API\Note;
use System\DB\Sql\Select;
use System\Module\AbstractModule;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const TIMETABLE_CHANGE_ALL = 'route:admin/health-center/doctors/timetable/change-status:all';
    const DOCTOR_VISIT_PATIENT = 'route:admin/health-center/doctor-panel/visit';
    const RESERVATION_CANCEL = 'route:admin/health-center/reservations/cancel';
    const RESERVATION_CANCEL_ALL = 'route:admin/health-center/reservations/cancel:all';
    const RESERVATION_DELETE = 'route:admin/health-center/reservations/delete';
    const PATIENT_PANEL = 'route:admin/health-center/patient-panel';
    const DOCTOR_PANEL = 'route:admin/health-center/doctor-panel';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();
        $class = $this;

        $em->attach('Note\API\Note', 'Note.Event.Visibility', function (Event $e) use ($class) {
            getSM('hc_event_manager')->onNoteEventVisibility($e);
        });

        $em->attach('Note\API\Note', 'Note.Event.Visibility.Filter', function (Event $e) use ($class) {
            getSM('hc_event_manager')->onNoteEventVisibilityFilter($e);
        });

        $em->attach('CustomersClub\API\Club', 'CC.CustomerRecords.Load', function (Event $e) {
            getSM('hc_event_manager')->onCC_CustomerRecords_Load($e);
        });

        $em->attach('User\API\EventManager', 'User.Delete', function (Event $e) {
            getSM('hc_event_manager')->onUserDelete($e);
        });

        $em->attach('Application\API\EventManager', 'Dashboard.Load', function (Event $e) {
            getSM('hc_event_manager')->onDashboardLoad($e);
        });

        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e)  {
            $lastRun = $e->getParam('last_run');
            getSM('hc_event_manager')->onCronRun($lastRun);
        });
    }
}