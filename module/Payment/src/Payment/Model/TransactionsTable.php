<?php
namespace Payment\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class TransactionsTable extends BaseTableGateway
{
    protected $table = 'tbl_payment_transactions';
    protected $model = 'Payment\Model\Transactions';
    protected $caches = null;
    protected $cache_prefix = null;

}
