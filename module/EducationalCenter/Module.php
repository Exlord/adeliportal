<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace EducationalCenter;

use Application\API\App;
use Application\Model\Config;
use Cron\API\Cron;
use EducationalCenter\Model;
use Mail\API\Mail;
use System\Module\AbstractModule;
use Theme\API\Common;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    const TIMETABLE_CHANGE_STATUS = 'route:admin/educational-center/workshop/class/timetable/change-status';
    const TIMETABLE_CHANGE_STATUS_ALL = 'route:admin/educational-center/workshop/class/timetable/change-status:all';
    const ATTENDANCE = 'route:admin/educational-center/workshop/class/attendance';
    const ATTENDANCE_CANCEL = 'route:admin/educational-center/workshop/class/attendance/cancel';
    const TIMETABLE = 'route:admin/educational-center/workshop/class/timetable';
    const USER_WORKSHOP_CLASSES = 'route:admin/educational-center/my-registered-workshop-classes';

    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('CustomersClub\API\Club', 'CC.CustomerRecords.Load', function (Event $e) {
            getSM('ec_event_manager')->onCC_CustomerRecords_Load($e);
        });

        $em->attach('User\API\EventManager', 'User.Delete', function (Event $e) {
            getSM('ec_event_manager')->onUserDelete($e);
        });

        $em->attach('Application\API\EventManager', 'Dashboard.Load', function (Event $e) {
            getSM('ec_event_manager')->onDashboardLoad($e);
        });

        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e)  {
            $lastRun = $e->getParam('last_run');
            getSM('ec_event_manager')->onCronRun($lastRun);
        });
    }
}


