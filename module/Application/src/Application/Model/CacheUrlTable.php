<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 3/11/14
 * Time: 11:50 AM
 */

namespace Application\Model;


use System\DB\BaseTableGateway;

class CacheUrlTable extends BaseTableGateway
{
    protected $table = 'tbl_cache_url';
    protected $model = 'Application\Model\CacheUrl';
    protected $caches = null;

    /**
     * @param $url
     * @return null|\Zend\Mvc\Router\RouteMatch
     */
    public function get($url)
    {
        $result = $this->select(array('url' => $url));
        if ($result && $result->count()) {
            return unserialize($result->current()->matchedRoute);
        }
        return null;
    }

    public function clear()
    {
        $q = "TRUNCATE TABLE `tbl_cache_url`";
        $this->getAdapter()->query($q)->execute();
    }
}