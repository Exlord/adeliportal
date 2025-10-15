<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace OnlineOrders;


use System\Module\AbstractModule;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;
use OnlineOrders\Model;
use OnlineOrders\API;

class Module extends AbstractModule
{
    public function getDir()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'groups_table' => function (ServiceManager $sm) {
                    return new Model\GroupsTable();
                },
                'language_online_order_table' => function (ServiceManager $sm) {
                    return new Model\LanguageTable();
                },
                'items_table' => function (ServiceManager $sm) {
                    return new Model\ItemsTable();
                },
                'groupItem_table' => function (ServiceManager $sm) {
                    return new Model\GroupItemTable();
                },
                'order_customer_table' => function (ServiceManager $sm) {
                    return new Model\CustomerTable();
                },
                'perDomains_table' => function (ServiceManager $sm) {
                    return new Model\PerDomainsTable();
                },
                'accountNumber_table' => function (ServiceManager $sm) {
                    return new Model\accountNumberTable();
                },
                'tree_function_api' => function (ServiceManager $sm) {
                    return new Api\TreeFunction();
                },
            )
        );
    }

   /* public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'order-tracking' => 'OnlineOrders\View\Helper\OrderTracking',
            )
        );
    }*/

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
    }
}


