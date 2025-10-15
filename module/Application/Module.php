<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\API\App;
use Application\API\Backup\Db;
use Application\Model\DbBackupTable;
use Components\API\Block;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Menu\API\Menu;
use Menu\Form\MenuItem;
use Notify\View\Helper\Notifications;
use System\Module\AbstractModule;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Form\Fieldset;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container as SessionContainer;
use Zend\Validator\AbstractValidator;
use Zend\Http\PhpEnvironment\Request as HttpRequest;

class Module extends AbstractModule
{
    const ADMIN = 'route:admin';
    const ADMIN_OPTIMIZATION = 'route:admin/optimization';
    const ADMIN_CACHE = 'route:admin/cache';
    const ADMIN_BACKUP = 'route:admin/backup';
    const ADMIN_BACKUP_DB = 'route:admin/backup/db';
    const ADMIN_BACKUP_DB_NEW = 'route:admin/backup/db/new';
    const ADMIN_BACKUP_DB_CREATE = 'route:admin/backup/db/create';
    const ADMIN_BACKUP_DB_RESTORE = 'route:admin/backup/db/restore';
    const ADMIN_BACKUP_DB_DELETE = 'route:admin/backup/db/delete';
    const ADMIN_CONFIGS = 'route:admin/configs';
    const ADMIN_CONFIGS_SYSTEM = 'route:admin/configs/system';
    const ADMIN_CONFIGS_SYSTEM_DELETE_FAV_ICON = 'route:admin/configs/system/delete-fav-icon';
    const ADMIN_CONFIGS_SYSTEM_DELETE_ADMIN_LOGO = 'route:admin/configs/system/delete-admin-logo';
    const ADMIN_CONFIGS_WIDGETS = 'route:admin/configs/widgets';
    const ADMIN_CONTENTS = 'route:admin/contents';
    const ADMIN_MODULES = 'route:admin/modules';
    const ADMIN_MODULES_REBUILD = 'route:admin/modules/rebuild';
    const ADMIN_STRUCTURE = 'route:admin/structure';
    const ADMIN_REPORTS = 'route:admin/reports';

//    const APP = 'route:app';

    const ADMIN_MAIL_TEMPLATE = 'route:admin/template';
    const ADMIN_MAIL_TEMPLATE_NEW = 'route:admin/template/new';
    const ADMIN_MAIL_TEMPLATE_EDIT = 'route:admin/template/edit';
    const ADMIN_MAIL_TEMPLATE_DELETE = 'route:admin/template/delete';

    const ADMIN_VIEW_UPDATES = 'route:admin:updates';


    public static $ServiceManager;
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;
    private $_cachedUriMatched = false;
    /**
     * @var \Application\Model\AliasUrlTable
     */
    private $_aliasUrlTable = null;
    /**
     * @var \Application\Model\CacheUrlTable
     */
    private $_cacheUrlTable = null;

    //TODO make resources for navigation pages
    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $sm = $e->getApplication()->getServiceManager();
        self::$ServiceManager = $sm;
        $adapter = $sm->get('db_adapter');
        GlobalAdapterFeature::setStaticAdapter($adapter);

        SessionContainer::setDefaultManager($sm->get('session_manager'));

        $em = StaticEventManager::getInstance();
        $em->attach('Zend\Mvc\Application', MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1000);
        $em->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', array($this, 'onDispatch'), -100);

        $vhm = $sm->get('ViewHelperManager');
        $pm = $vhm->get('Navigation')->getPluginManager();
        $pm->setInvokableClass('menu', 'Application\View\Helper\Navigation\Menu');

        $this->_aliasUrlTable = getSM('alias_url_table');
        $this->_cacheUrlTable = getSM('cache_url_table');
        $this->onBeforeRoute($e);

        $em = StaticEventManager::getInstance();
        if (isAllowed(self::ADMIN_VIEW_UPDATES)) {
            $em->attach('Notify\View\Helper\Notifications', 'Notification.Load.Bar', function (Event $e) {
                getSM('application_event_manager')->onLoadNotificationBar($e);
            });
        }

        //CRON
        $em->attach('Cron\API\Cron', Cron::CRON_RUN, function (Event $e) {
            $last_run = $e->getParam('last_run');
            getSM('application_event_manager')->onCronRun($last_run);
        });

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('application_event_manager')->onLoadBlockConfigs($e);
        });

        $em->attach('Menu\API\Menu', Menu::LOAD_MENU_TYPES, function (Event $e) {
            getSM('application_event_manager')->onLoadMenuTypes($e);
        });
    }

    public function onBeforeRoute(MvcEvent $e)
    {
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $e->getRequest();
        if ($request instanceof HttpRequest) {
            $uri = $request->getRequestUri();

            if ($this->_aliasUrlTable) {
                $unicodeUri = urldecode($uri);
                $aliasMatched = $this->_aliasUrlTable->getByAlias($unicodeUri);
                if ($aliasMatched) {
                    $request->setUri($aliasMatched);
                }
            }

            if ($this->_cacheUrlTable) {
                $matchedRoute = $this->_cacheUrlTable->get($uri);
                if ($matchedRoute && $matchedRoute instanceof RouteMatch) {
                    $this->_cachedUriMatched = true;
                    $e->setRouteMatch($matchedRoute);

                    //detach the onRoute event from routeListener
                    $e->getApplication()
                        ->getServiceManager()
                        ->get('RouteListener')
                        ->detach($e->getApplication()->getEventManager());
                }
            }
        }
    }

    public function onDispatch(MvcEvent $e)
    {
        $vhm = getSM('ViewHelperManager');
        /* @var $basePath callable */
        $basePath = $vhm->get('basePath');
        $vhm->get('headScript')->appendFile($basePath() . '/js/ie8-warning.js', 'text/javascript',
            array('conditional' => 'lt IE 9'));
    }

    public function onRoute(MvcEvent $e)
    {
        if (!$this->_cachedUriMatched) {
            /* @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $e->getRequest();
            if ($request instanceof HttpRequest) {
                if ($this->_cacheUrlTable) {
                    $uri = $request->getRequestUri();
                    $matchedRoute = $e->getRouteMatch();
                    $this->_cacheUrlTable->save(array('url' => $uri, 'matchedRoute' => serialize($matchedRoute)));
                }
            }
        }
    }
}
