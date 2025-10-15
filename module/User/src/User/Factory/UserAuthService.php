<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/8/13
 * Time: 1:06 PM
 */

namespace User\Factory;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserAuthService implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $authService = new AuthenticationService();
        $session = new Session(null, null, $serviceLocator->get('session_manager'));
        $authService->setStorage($session);
        return $authService;
    }
} 