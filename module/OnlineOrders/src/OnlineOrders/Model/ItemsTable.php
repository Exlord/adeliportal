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
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway;
use Zend\Db\Sql\Select;

class ItemsTable extends BaseTableGateway
{
    protected $table = 'tbl_order_form_items';
    protected $model = 'OnlineOrders\Model\Items';
    protected $caches = null;
    protected $cache_prefix = null;



    public function getItem($id)
    {
        $adapter = getSM()->get('db_adapter');
        $select = new Select();
        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from(array('i' => 'tbl_order_form_items'))
            ->where(array('v.groupId'=>$id))// base table
            -> join(array('v' => 'tbl_order_form_groups_items'),     // join table with alias
                'i.id = v.itemId',array('groupId'));

        $select->order(array('itemPosition DESC','id DESC'));


        $selectString = $sql->getSqlStringForSqlObject($select);

        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }
}
