<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Payment\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class PaymentTable extends BaseTableGateway
{
    protected $table = 'tbl_payment';
    protected $model = 'Payment\Model\Payment';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getData($id)
    {
        $select = $this->get($id);
        return unserialize($select->data);
    }

    public function getStatus($id)
    {
        $select = $this->getAll(array(
            'id'=>$id,
            'status'=>1
        ))->current();
        if($select)
            return unserialize($select->data);
        else
            return false;
    }

}
