<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Sample\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class SampleTable extends BaseTableGateway
{
    protected $table = '';
    protected $model = 'Sample\Model\SampleModel';
    protected $caches = null;
    protected $cache_prefix = null;

}
