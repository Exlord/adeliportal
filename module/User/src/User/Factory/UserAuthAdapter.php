<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/8/13
 * Time: 1:06 PM
 */

namespace User\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Authentication\Adapter\DbTable as AuthAdapter;

class UserAuthAdapter implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        /* @var $user_table \User\Model\UserTable */
        $user_table = $serviceLocator->get('user_table');
        $authAdapter = new AuthAdapter($user_table->getAdapter());
        $authAdapter->setTableName($user_table->getTable());
        $authAdapter->setIdentityColumn('username');
        $authAdapter->setCredentialColumn('password');
        return $authAdapter;
    }
} 