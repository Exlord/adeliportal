<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace ECommerce\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class ProductTable extends BaseTableGateway
{
    protected $table = 'tbl_commerce_product';
//    protected $model = 'ECommerce\Model\Product';
    protected $caches = null;
    protected $cache_prefix = null;
}
