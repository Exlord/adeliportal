<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace ClientManager\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class LicenseTable extends BaseTableGateway
{
    protected $table = 'tbl_licenses';
    protected $model = 'ClientManager\Model\License';
    protected $caches = null;
    protected $cache_prefix = null;

    /**
     * @param $license
     * @return License
     */
    public function getByLicense($license)
    {
        $data = $this->select(array('data' => $license,));
        if($data)
            return $data->current();

        return null;
    }
}
