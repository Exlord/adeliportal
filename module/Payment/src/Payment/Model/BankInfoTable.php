<?php
namespace Payment\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class BankInfoTable extends BaseTableGateway
{
    protected $table = 'tbl_payment_bank_info';
    protected $model = 'Payment\Model\BankInfo';
    protected $caches = null;
    protected $cache_prefix = null;


    public function getBankName()
    {
        $data = array();
        $select = $this->getAll(array('status'=>1));//TODO Update Select
        foreach($select as $row)
        {
            $data[$row->className]=$row->name;
        }
        return $data;
    }
}
