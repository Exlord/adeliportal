<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace SimpleOrder\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class SimpleOrderTable extends BaseTableGateway
{
    protected $table = 'tbl_simple_order';
    protected $model = 'SimpleOrder\Model\SimpleOrder';
    protected $caches = null;
    protected $cache_prefix = null;

    public static $quantity = array(
        0 => '-- Select --',
        1 => 'simpleOrder_select_item_ton',
        2 => 'simpleOrder_select_item_kg',
        3 => 'simpleOrder_select_item_num',
    );
}


