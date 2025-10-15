<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ajami
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Note;

use Application\Model\Config;
use Cron\API\Cron;
use Sample\Model;
use System\Module\AbstractModule;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const NOTE_VIEW_ALL = 'route:admin/note:all';
    const NOTE_EDIT = 'route:admin/note/edit';
    const NOTE_EDIT_ALL = 'route:admin/note/edit:all';
    const NOTE_DELETE = 'route:admin/note/delete';
    const NOTE_DELETE_ALL = 'route:admin/note/delete:all';
    const NOTE_NEW = 'route:admin/note/new';
    const NOTE_NEW_ALL = 'route:admin/note/new:all';
}


