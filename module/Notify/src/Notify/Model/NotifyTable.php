<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Notify\Model;

use System\DB\BaseTableGateway;
use System\Model\BaseModel;
use Zend\Db;
use Zend\Db\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class NotifyTable extends BaseTableGateway
{
    protected $table = 'tbl_notify';
    protected $model = 'Notify\Model\Notify';
    protected $caches = null;
    protected $cache_prefix = null;

    public function save($model)
    {
        if (is_array($model->uId)) {
            $models = array();
            foreach ($model->uId as $uid) {
                $models[] = array('uId' => $uid, 'msg' => $model->msg, 'date' => $model->date);
            }
            $this->multiSave($models);
        } else
            return parent::save($model);
    }


}
