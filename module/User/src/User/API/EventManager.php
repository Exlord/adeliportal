<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/13/2014
 * Time: 11:55 AM
 */

namespace User\API;


use Application\Model\Config;
use Cron\API\Cron;
use Menu\Form\MenuItem;
use System\API\BaseAPI;
use User\Permissions\Acl\AclManager;
use Zend\EventManager\Event;
use Zend\Mvc\MvcEvent;
use Zend\Http\Response as HttpResponse;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ViewModel;

class EventManager extends BaseAPI
{
    public function newUserIsCreated($userId)
    {
        $this->getEventManager()->trigger('User.New', $this, array('userId' => $userId));
    }

    public function userIsDeleted($userId)
    {
        $this->getEventManager()->trigger('User.Delete', $this, array('userId' => $userId));
    }

    public function onRoute(Event $event)
    {
        $sm = $event->getApplication()->getServiceManager();
        $routeMatch = $event->getRouteMatch();
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');
        $route = $routeMatch->getMatchedRouteName();

        $acl = AclManager::load();
        $resource = 'route:' . $route;
        $userIdentity = current_user();
        \Zend\View\Helper\Navigation::setDefaultAcl($acl);
        $allowed = $acl->isAllowed(null, $resource);
        if (!$allowed && !($action == 'access-denied' && $route == 'app/user/auth')) {
            $isAjax = $event->getRequest()->isXmlHttpRequest();
            if ($isAjax) {
                $event->setError('error-unauthorized-route')
                    ->setParam('route', $route)
                    ->setParam('controller', $controller)
                    ->setParam('action', $action)
                    ->setParam('identity', $userIdentity)
                    ->setParam('isAjax', $isAjax);
                $event->getTarget()->getEventManager()->trigger('dispatch.error', $event);
            } else {
                $newRouteMatch = null;
                if ($userIdentity->id) {
                    $url = url('app/user', array(), array(
                        'query' => array(
                            'status' => 'access-denied'
                        )
                    ));
                } else {
                    $url = url('app/user/login', array(), array(
                        'query' => array(
                            'redirect' => urlencode($event->getRequest()->getRequestUri()),
                            'status' => 'access-denied'
                        )
                    ));
                }

                $response = $event->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();
                return $response;
            }
        }
    }

    public function onDispatchError(Event $event)
    {
        // Do nothing if the result is a response object
        $result = $event->getResult();
        if ($result instanceof Response) {
            return;
        }

        // Common view variables
        $viewVariables = array(
            'error' => $event->getParam('error'),
        );

        $error = $event->getError();
        switch ($error) {
            case 'error-unauthorized-route':
                $viewVariables['route'] = $event->getParam('route');
                $viewVariables['identity'] = $event->getParam('identity');
                $viewVariables['controller'] = $event->getParam('controller');
                $viewVariables['action'] = $event->getParam('action');
                break;
            default:
                //$event->getViewModel()->setTerminal('true');
                /*
                 * do nothing if there is no error in the event or the error
                 * does not match one of our predefined errors (we don't want
                 * our 403.phtml to handle other types of errors)
                 */
                return;
        }
        $model = new ViewModel($viewVariables);
//        $model->setTemplate('error/403');
        $model->setTemplate('user/user/access-denied');
//        $model->setTerminal($event->getParam('isAjax'));
//        if ($event->getParam('isAjax'))
//            $event->setViewModel($model);
//        else {
        $event->getViewModel()->addChild($model);
//            $event->getViewModel()->setTerminal($event->getRequest()->isXmlHttpRequest());
//        }

        $response = $event->getResponse();
        if (!$response) {
            $response = new HttpResponse();
            $event->setResponse($response);
        }
        $response->setStatusCode(403);
    }

    public function onCronRun(Config $last_run)
    {
        $start = microtime(true);
        $last = @$last_run->varValue[__NAMESPACE__ . '_expired_flood_clear'];
        $interval = '+1 month';

        if (Cron::ShouldRun($interval, $last)) {

            getSM('user_flood_table')->clearExpired();

            db_log_info(sprintf(t('User expired flood entries clearing cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue[__NAMESPACE__ . '_expired_flood_clear'] = time();
        }
    }

    public function onLoadMenuTypes(Event $event)
    {
        /* @var $form MenuItem */
        $form = $event->getParam('form');

        $form->menuTypes['user'] = array(
            'label' => 'Current User',
            'note' => "Link to current user's profile",
            'params' => array(array('route' => 'app/user'),),
        );

//        $form->menuTypes['user-profile'] = array(
//            'label' => 'User Profile',
//            'note' => "Link to a user's profile",
//            'data-url' => url('admin/menu-users-list', array('type' => 'article')),
//            'params' => array(
//                array('route' => 'app/user/user-profile'),
//                'id',
//            ),
//            'template' => '[id] - [username]',
//        );

        $form->menuTypes['login'] = array(
            'label' => 'User Login',
            'note' => 'user login page',
            'params' => array(array('route' => 'app/user/login'),),
        );

        $form->menuTypes['logout'] = array(
            'label' => 'User Logout',
            'note' => 'user logout page',
            'params' => array(array('route' => 'app/user/logout'),),
        );

        $form->menuTypes['register'] = array(
            'label' => 'User Register',
            'note' => 'user register page',
            'params' => array(array('route' => 'app/user/register'),),
        );

        $form->menuTypes['password-recovery'] = array(
            'label' => 'User Password Recovery',
            'note' => 'user password recovery page',
            'params' => array(array('route' => 'app/user/password-recovery'),),
        );
    }
} 