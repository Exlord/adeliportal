<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace ProductShowcase\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway;
use Zend\Paginator\Paginator;

class PsCartTable extends BaseTableGateway
{
    protected $table = 'tbl_product_showcase_cart';
    protected $model = 'ProductShowcase\Model\PsCart';
    protected $caches = null;
    protected $cache_prefix = null;
}
