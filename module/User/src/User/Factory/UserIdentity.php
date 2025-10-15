<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/8/13
 * Time: 1:06 PM
 */

namespace User\Factory;

use User\Model\User;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserIdentity implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        /* @var $auth_service \Zend\Authentication\AuthenticationService */
        $auth_service = $serviceLocator->get('user_auth_service');

        if ($auth_service->hasIdentity()) {
            return $auth_service->getIdentity();
        } else
            return $this->userGuest();
    }

    private function userGuest(){
        $guest = new User();
        $guest->id = 0;
        $guest->displayName = 'Guest';
        $guest->roles = array(array('id' => 1, 'roleName' => 'Guest'));
        $guest->username = 'Guest';
        return $guest;
    }
} 