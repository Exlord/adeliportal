<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/9/13
 * Time: 12:14 PM
 */

namespace Application\Model;


use System\DB\BaseTableGateway;
use Zend\Db\Sql\Select;

class DbBackupTable extends BaseTableGateway
{
    protected $model = 'Application\Model\DbBackup';
    protected $table = 'tbl_db_backup';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getOldBackupsByDate($date)
    {
        return $this->select(array('created < ?' => $date));
    }

    public function getBackupsOlderThanByCount($count)
    {
        $select = $this->getSql()->select();
        $select
            ->columns(array('file', 'id', 'size'))
            ->order('created DESC')
            ->offset($count);
        return $this->selectWith($select);
    }

    public function getOldBackupsByCount($count)
    {
        $select = $this->getSql()->select();
        $select
            ->columns(array('file', 'id', 'size'))
            ->order('created ASC')
            ->limit($count);
        return $this->selectWith($select);
    }

    public function getLast(){
        $select = $this->getSql()->select();
        $select
            ->columns(array('file', 'id', 'size'))
            ->order('created ASC')
            ->limit(1);
        return $this->selectWith($select);
    }
}