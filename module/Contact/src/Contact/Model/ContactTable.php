<?php

namespace Contact\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class ContactTable extends BaseTableGateway
{
    protected $table = 'tbl_contact';
    protected $model = 'Contact\Model\Contact';
    protected $caches = null;
    protected $cache_prefix = null;
}
