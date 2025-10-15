<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace EducationalCenter\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;
use Zend\Stdlib\ArrayObject;

class WorkshopTimeTable extends BaseTableGateway
{
    protected $table = 'tbl_ec_workshop_timetable';
    protected $model = 'EducationalCenter\Model\WorkshopTime';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getFirstSession($class)
    {
        $this->swapResultSetPrototype();
        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->columns(
                array(
                    'firstSession' => new Db\Sql\Predicate\Expression("MIN(`start`)")
                ))
            ->where(array(
                $this->table . '.classId' => $class,
                $this->table . '.status' => 0,
            ));
        $result = $this->selectWith($select);
        $this->swapResultSetPrototype();
        if ($result) {
            $result = $result->current();
            if ($result) {
                return (int)$result['firstSession'];
            }
        }
        return false;
    }

    public function removeByClass($class)
    {
        $this->delete(array('classId' => $class));
    }
}
