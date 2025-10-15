<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/8/13
 * Time: 1:06 PM
 */

namespace Application\Factory;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session;

class SessionManager implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $adapter = $serviceLocator->get('db_adapter');
        $config = $serviceLocator->get('ApplicationConfig');
        $sessionOptions = new Session\SaveHandler\DbTableGatewayOptions();
        $sessionTableGateway = new TableGateway('tbl_session', $adapter);
        $saveHandler = new Session\SaveHandler\DbTableGateway($sessionTableGateway, $sessionOptions);
        $sessionConfig = new Session\Config\SessionConfig();
//                    $sessionConfig->setCookieDomain(ACTIVE_SITE);
//                    $sessionConfig->setCookieSecure(true);
        $sessionConfig->setOptions($config['session']);
        $sessionConfig->setCookieLifetime(2419200);
        $sessionConfig->setUseCookies(true);
        $sessionConfig->setGcMaxlifetime(2419200);
        // $sessionConfig->setCookieLifetime($sessionConfig->getRememberMeSeconds());
        $sessionManager = new Session\SessionManager($sessionConfig, NULL, $saveHandler);
        $sessionManager->getValidatorChain()->attach('session.validate', array(new Session\Validator\HttpUserAgent(), 'isValid'));
        $sessionManager->getValidatorChain()->attach('session.validate', array(new Session\Validator\RemoteAddr(), 'isValid'));
        $sessionManager->start();
        return $sessionManager;
    }
} 