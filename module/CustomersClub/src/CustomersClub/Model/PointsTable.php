<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace CustomersClub\Model;

use System\DB\BaseTableGateway;
use System\Model\BaseModel;
use Zend\Db;
use Zend\Db\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class PointsTable extends BaseTableGateway
{
    protected $table = 'tbl_cc_points';
    protected $model = 'CustomersClub\Model\Point';
    protected $caches = null;
    protected $cache_prefix = null;


    public function save($model, $pointBefore = null)
    {
        $model = (array)$model;
        unset($model['type']);
        unset($model['filters']);
        $type = (isset($model['id']) && $model['id']) ? 'edit' : 'new';
        $pointsToAdd = (int)$model['points'];
        if ($pointBefore)
            $pointsToAdd = $pointsToAdd - $pointBefore;

        $id = parent::save($model);
        getSM('points_total_table')->save($model['userId'], $pointsToAdd);
        getSM('points_api')->notify($model['userId'], $model['points'], $model['note'], $type, $pointBefore);
        return $id;
    }
}
