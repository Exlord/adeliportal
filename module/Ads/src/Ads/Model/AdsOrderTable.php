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

class AdsOrderTable extends BaseTableGateway
{
    protected $table = 'tbl_ads_order';
    protected $model = 'Ads\Model\AdsOrder';
    protected $caches = null;
    protected $cache_prefix = null;

    public function saveOrder($dataSort)
    {
        if ($dataSort) {
            $secondIds = array();
            foreach ($dataSort as $row)
                $secondIds[] = $row['secondType'];
            $this->delete(array('secondType' => $secondIds));
            $this->multiSave($dataSort);
        }
    }
}
