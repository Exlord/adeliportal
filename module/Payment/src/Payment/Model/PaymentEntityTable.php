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

class PaymentEntityTable extends BaseTableGateway
{
    protected $table = 'tbl_payment_entity';
    protected $model = 'Payment\Model\PaymentEntity';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getPaymentCount($userId)
    {
        $this->swapResultSetPrototype();
        $select = $this->getSql()->select();
        $select
            ->columns(array('count' => new Db\Sql\Predicate\Expression('COUNT(paymentId)')))
            ->where(array('userId' => $userId));

        $result = $this->selectWith($select);
        $this->swapResultSetPrototype();

        return $result;
    }
}
