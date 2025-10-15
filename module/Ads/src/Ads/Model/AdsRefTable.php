<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Ads\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class AdsRefTable extends BaseTableGateway
{
    protected $table = 'tbl_ads_ref';
    protected $model = 'Ads\Model\AdsRef';
    protected $caches = null;
    protected $cache_prefix = null;

    public function searchByAdId($adId)
    {
        if ($adId) {
            $select = $this->getAll(array('adId' => $adId));
            if ($select)
                return $select;
        }
        return false;
    }
}
