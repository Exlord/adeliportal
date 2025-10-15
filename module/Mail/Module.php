<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Mail;

use Sample\Model;
use System\Module\AbstractModule;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const ENTITY_TYPE_QUICK_SEND_MAIL = 'quick_send_mail';

    const APP_QUICK_SEND_MAIL = 'route:app/quick-send-mail';

    const ADMIN_MAIL_CONFIGS = 'route:admin/configs';
    const ADMIN_MAIL_CONFIGS_MAIL = 'route:admin/configs/mail';
    const ADMIN_MAIL = 'route:admin/mail';

    const ADMIN_MAIL_DELETE = 'route:admin/mail/delete';
    const ADMIN_MAIL_ARCHIVE = 'route:admin/mail/archive';
    const ADMIN_MAIL_ARCHIVE_DELETE = 'route:admin/mail/archive/delete';
}


