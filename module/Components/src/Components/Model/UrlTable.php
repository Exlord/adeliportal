<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 3/11/14
 * Time: 11:50 AM
 */

namespace Components\Model;


use System\DB\BaseTableGateway;

class UrlTable extends BaseTableGateway
{
    protected $table = 'tbl_blocks_per_url';
    protected $model = 'Components\Model\Url';
    protected $caches = null;

    public function get($url)
    {
        $result = $this->select(array('url' => $url));
        if ($result && $result->count()) {
            return unserialize($result->current()->blocks);
        }
        return null;
    }

    public function clear()
    {
        $q = "TRUNCATE TABLE `tbl_blocks_per_url`";
        $this->getAdapter()->query($q)->execute();
    }
}