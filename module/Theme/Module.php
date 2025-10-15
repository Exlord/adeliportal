<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Theme;

use Components\API\Block;
use Components\Form\NewBlock;
use System\IO\Directory;
use System\Module\AbstractModule;
use Theme\API\Themes;
use Zend\EventManager\Event;
use Zend\EventManager\StaticEventManager;
use Zend\Form\Fieldset;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
        $em = StaticEventManager::getInstance();
        $em->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', array($this, 'onDispatch'), 100);
//        $em->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), -10001);

        $em->attach('Components\API\Block', Block::LOAD_BLOCK_CONFIGS, function (Event $e) {
            getSM('theme_event_manager')->onLoadBlockConfigs($e);
        });
    }

    public function onDispatchError(MvcEvent $e)
    {
        if (!($e->getRequest() instanceof ConsoleRequest)) {
            self::setTemplate($e, null);
        }
    }

    public function onDispatch(MvcEvent $e)
    {
        if (!($e->getRequest() instanceof ConsoleRequest)) {
            $controller = $e->getTarget();
            self::setTemplate($e, $controller);
        }
    }

    public static function setTemplate(MvcEvent $e, $controller)
    {
        $sm = $e->getApplication()->getServiceManager();
        $config = $e->getApplication()->getServiceManager()->get('Config');


        $adminTheme = Themes::getAdminTheme();
        $clientTheme = Themes::getClientTheme();

        $route = $e->getRouteMatch();
        if (!is_null($route)) {
            $route = $route->getMatchedRouteName();
        } else
            $route = $e->getRequest()->getUri();

        if (strpos($route, 'admin') > -1) {
            if (!is_null($controller)) {
//                var_dump($e->getRequest()->getHeaders());
                if ($e->getRequest()->isXmlHttpRequest())
                    $controller->layout('layout/ajax_layout_admin.phtml');
                else
                    $controller->layout('layout/layout.phtml');
            }

            $client_theme = $adminTheme->name;

        } else {
            if (!is_null($controller)) {
                $controllerClass = get_class($controller);
                $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
                if ($e->getRequest()->isXmlHttpRequest())
                    $controller->layout('layout/ajax_layout_client.phtml');
                else {
                    if (isset($config['module_layouts'][$moduleNamespace])) {
                        $controller->layout($config['module_layouts'][$moduleNamespace]);
                    } else
                        $controller->layout('layout/layout.phtml');
                }
            }

            $client_theme = $clientTheme->name;
        }

        /* @var $request \Zend\Http\PhpEnvironment\Request) */
        $request = $e->getRequest();
        $forcedTemplate = $request->getQuery('template', false);
        if ($forcedTemplate)
            $client_theme = $forcedTemplate;

        $client_theme_url = '/themes/' . $client_theme;
        $client_theme_path = ROOT . '/module/Theme/public' . $client_theme_url . "/templates";

        $templatePathResolver = $sm->get('Zend\View\Resolver\TemplatePathStack');
        $templatePathResolver->setOptions(
            array(
                'script_paths' => array(
                    $client_theme_path,
                )
            )
        );

        defined('TEMPLATE_PATH') or define('TEMPLATE_PATH', $client_theme_url);
        defined('TEMPLATE_NAME') or define('TEMPLATE_NAME', $client_theme);
    }
}