<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Payment;

use Menu\API\Menu;
use Menu\Form\MenuItem;
use Payment\Model;
use System\Module\AbstractModule;
use Zend\Db\Adapter\Adapter;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const APP_PAYMENT = 'route:app/payment';
    const ADMIN_PAYMENT = 'route:admin/payment';
    // const ADMIN_PAYMENT_SEND_PAYMENT = 'route:admin/payment/send-payment';
    const ADMIN_PAYMENT_MY_PAYMENT = 'route:admin/payment/my-payments';
    const ADMIN_PAYMENT_BANK_INFO = 'route:admin/payment/bank-info';
    const ADMIN_PAYMENT_BANK_INFO_NEW = 'route:admin/payment/bank-info/new';
    const ADMIN_PAYMENT_BANK_INFO_EDIT = 'route:admin/payment/bank-info/edit';
    const ADMIN_PAYMENT_BANK_INFO_DELETE = 'route:admin/payment/bank-info/delete';
    const ADMIN_PAYMENT_BANK_INFO_UPDATE = 'route:admin/payment/bank-info/update';
    const ADMIN_PAYMENT_TRANSACTIONS = 'route:admin/payment/transactions';
    const ADMIN_PAYMENT_TRANSACTIONS_CONFIG = 'route:admin/payment/transactions/config';
    const ADMIN_PAYMENT_TRANSACTIONS_VIEW_ALL = 'route:admin/payment/transactions:viewAll';
    const ADMIN_PAYMENT_TRANSACTIONS_NEW_DIRECT_DEPOSIT = 'route:admin/payment/transactions/new:directDeposit';
    const PAYMENT_TRANSACTIONS_NEW = 'route:admin/payment/transactions/new';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('CustomersClub\API\Club', 'CC.Points.Config.Load', function (Event $e) {
            getSM('payment_event_manager')->onCC_PointsConfigLoad($e);
        });

        $em->attach('User\API\EventManager', 'User.New', function (Event $e) {
            getSM('payment_event_manager')->onNewUser($e);
        });

        $em->attach('CustomersClub\API\Club', 'CC.CustomerRecords.Load', function (Event $e) {
            getSM('payment_event_manager')->onCC_CustomerRecords_Load($e);
        });

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('payment_event_manager')->onLoadMenuTypes($e);
        });
    }
}