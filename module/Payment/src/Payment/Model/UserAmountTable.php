<?php
namespace Payment\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class UserAmountTable extends BaseTableGateway
{
    protected $table = 'tbl_payment_user_amount';
    protected $model = 'Payment\Model\UserAmount';
    protected $caches = null;
    protected $cache_prefix = null;

    public function updateOrInsertAmount($userIdArray, $amount)
    {
        $allQuery = '';
        $cash = $this->getAdapter()->getPlatform()->quoteIdentifier('cash');
        $qTemp = "INSERT INTO %s (%s,%s) VALUES (%s,%s) ON DUPLICATE KEY UPDATE %s=%s+%s ;";
        foreach ($userIdArray as $val) {
            $q = sprintf($qTemp,
                $this->getAdapter()->getPlatform()->quoteIdentifier($this->table),
                $this->getAdapter()->getPlatform()->quoteIdentifier('userId'),
                $cash,
                $val, $amount,
                $cash, $cash, $amount
            );
            $allQuery .= $q;
        }
        $this->getAdapter()->query($allQuery)->execute();
        return true;
    }

    public function getAmount($userId)
    {
        $amount = 0;
        if ($userId) {
            $select = $this->getAll(array('userId' => $userId))->current();
            if (isset($select->cash))
                $amount = $select->cash;
        }
        return $amount;
    }
}
