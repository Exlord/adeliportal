<?php
namespace OrgChart;

use Application\Model\Config;
use Cron\API\Cron;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use OrgChart\Model;
use System\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const APP_CHART = 'route:app/chart';

    const ADMIN_ORG_CHART = 'route:admin/org-chart';
    const ADMIN_ORG_CHART_CONFIG = 'route:admin/org-chart/config';
    const ADMIN_ORG_CHART_CHART_LIST = 'route:admin/org-chart/chart-list';
    const ADMIN_ORG_CHART_NEW = 'route:admin/org-chart/new';
    const ADMIN_ORG_CHART_EDIT = 'route:admin/org-chart/edit';
    const ADMIN_ORG_CHART_UPDATE = 'route:admin/org-chart/update';
    const ADMIN_ORG_CHART_DELETE = 'route:admin/org-chart/delete';
    const ADMIN_CHART_NODE = 'route:admin/chart-node';
    const ADMIN_CHART_NODE_PARENT_NODE = 'route:admin/chart-node/parent-node';
    const ADMIN_CHART_NODE_NEW = 'route:admin/chart-node/new';
    const ADMIN_CHART_NODE_EDIT = 'route:admin/chart-node/edit';
    const ADMIN_CHART_NODE_UPDATE = 'route:admin/chart-node/update';
    const ADMIN_CHART_NODE_DELETE = 'route:admin/chart-node/delete';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('org_chart_event_manager')->onLoadMenuTypes($e);
        });
    }
}