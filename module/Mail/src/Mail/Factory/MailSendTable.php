<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/8/13
 * Time: 1:06 PM
 */

namespace Mail\Factory;

use Mail\Model\MailTable;
use Mail\Model\SendTable;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MailSendTable implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        return new SendTable($serviceLocator->get('mail_db_adapter'));
    }
} 