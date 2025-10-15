<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 1:07 PM
 */
namespace Logger\Model;

use Zend\Db;
use Zend\Db\TableGateway;
use System;

/*
EMERG   = 0;  // Emergency: system is unusable
ALERT   = 1;  // Alert: action must be taken immediately
CRIT    = 2;  // Critical: critical conditions
ERR     = 3;  // Error: error conditions
WARN    = 4;  // Warning: warning conditions
NOTICE  = 5;  // Notice: normal but significant condition
INFO    = 6;  // Informational: informational messages
DEBUG   = 7;  // Debug: debug messages
 */
class LogTable extends \System\DB\BaseTableGateway
{
    protected $table = 'tbl_event_logs';
    protected $model = 'Logger\Model\Log';
    protected $caches = null;

    public function log($priority, $message, $entityType = null)
    {
        $log = new Log();
        $log->priority = $priority;
        $log->message = $message;
        $log->uid = current_user()->id;
        $log->timestamp = time();
        $log->entityType = $entityType;
        $this->save($log);
        $log = null;
    }

    public function getByEntityType($entityType, $pageNumber = false)
    {
        return $this->getAll(array('entityType' => $entityType), null, null, $pageNumber);
    }
}
