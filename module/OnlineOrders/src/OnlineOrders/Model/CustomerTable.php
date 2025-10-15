<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace OnlineOrders\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class CustomerTable extends BaseTableGateway
{
    protected $table = 'tbl_order_form_customer';
    protected $model = 'OnlineOrders\Model\Customer';
    protected $caches = null;
    protected $cache_prefix = null;
}


