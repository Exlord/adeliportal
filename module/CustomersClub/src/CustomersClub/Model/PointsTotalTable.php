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

class PointsTotalTable extends BaseTableGateway
{
    protected $table = 'tbl_cc_points_total';
    protected $caches = null;
    protected $cache_prefix = null;
    protected $primaryKey = 'userId';

    public function getMyPoints($userId)
    {
        $select = $this->getSql()->select();
        $select
            ->columns(array('points'))
            ->where(array('userId' => $userId));
        $result = $this->selectWith($select);

        if ($result) {
            $result = $result->current();
            if ($result)
                return (int)$result['points'];
        }
        return 0;
    }

    public function save($userId, $points)
    {
        $pointsColumn = $this->getAdapter()->getPlatform()->quoteIdentifier('points');
        $q = "INSERT INTO %s (%s,%s) VALUES (?,?) ON DUPLICATE KEY UPDATE %s=%s+?";
        $q = sprintf($q,
            $this->getAdapter()->getPlatform()->quoteIdentifier($this->table),
            $this->getAdapter()->getPlatform()->quoteIdentifier('userId'),
            $pointsColumn,
            $pointsColumn,
            $pointsColumn
        );

        $this->getAdapter()->query($q, array(
            $userId, $points, $points
        ));
    }
}
