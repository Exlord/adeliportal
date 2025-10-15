<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace CustomersClub\Model;

use System\DB\BaseTableGateway;
use System\Model\BaseModel;
use Zend\Db;
use Zend\Db\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class CustomerRecordsTable extends BaseTableGateway
{
    protected $table = 'tbl_cc_customer_records';
    protected $caches = null;
    protected $cache_prefix = null;

    public function add($userId, $note)
    {
        $this->insert(array(
            'userId' => $userId,
            'note' => $note,
            'date' => time()
        ));
    }
}
