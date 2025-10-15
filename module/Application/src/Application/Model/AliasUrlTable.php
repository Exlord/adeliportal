<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 3/11/14
 * Time: 11:50 AM
 */

namespace Application\Model;


use System\DB\BaseTableGateway;

class AliasUrlTable extends BaseTableGateway
{
    protected $table = 'tbl_alias_url';
    protected $model = 'Application\Model\AliasUrl';
    protected $caches = null;

    /**
     * @param $url
     * @return null|string
     */
    public function getByAlias($url)
    {
        $result = $this->select(array('alias' => $url));
        if ($result && $result->count()) {
            return $result->current()->url;
        }
        return null;
    }

    public function getByUrl($url)
    {

        $result = $this->select(array('url' => $url));
        if ($result && $result->count()) {
            return $result->current()->alias;
        }
        return null;
    }
}