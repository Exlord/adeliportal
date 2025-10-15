<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace PhoneBook;

use PhoneBook\Model;
use System\Module\AbstractModule;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const ADMIN_PHONE_BOOK = 'route:admin/phone-book';
    const ADMIN_PHONE_BOOK_NEW = 'route:admin/phone-book/new';
    const ADMIN_PHONE_BOOK_EDIT = 'route:admin/phone-book/edit';
    const ADMIN_PHONE_BOOK_DELETE = 'route:admin/phone-book/delete';
    const ADMIN_PHONE_BOOK_UPDATE = 'route:admin/phone-book/update';
    const ADMIN_PHONE_BOOK_SEND_SMS = 'route:admin/phone-book/send-phone-book-sms';
    const ADMIN_PHONE_BOOK_SEND_EMAIL = 'route:admin/phone-book/send-phone-book-email';
    const ADMIN_PHONE_BOOK_WORD_EXPORT = 'route:admin/phone-book/word-export';
    const ADMIN_PHONE_BOOK_PRINT = 'route:admin/phone-book/print';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);
    }
}