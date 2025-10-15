<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/5/12
 * Time: 10:25 AM
 */
namespace Application\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class ModulesTable extends BaseTableGateway
{
    protected $table = 'tbl_modules';
    protected $model = 'Application\Model\Modules';
    protected $caches = array('installed_modules');

    public function getArray()
    {
//        $cacheKey = 'installed_modules';
//        if (cacheExist($cacheKey))
//            $list = getCacheItem($cacheKey);
//        else {
            $data = $this->getAll();
            $list = array();
            foreach ($data as $row) {
                $list[$row->name] = $row;
            }
//            setCacheItem($cacheKey, $list);
//        }
        return $list;
    }
}
