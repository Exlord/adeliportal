<?php
namespace Gallery\Model;

use System\DB\BaseTableGateway;
use Zend\Db\Sql\Select;

class BannerTable extends BaseTableGateway
{
    protected $table = 'tbl_banner';
    protected $model = 'Gallery\Model\Banner';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getGroupIdArray($position, $count)
    {
        if ($position) {
            $select = $this->getSql()->select();
            $select->columns(array('groupId'))
                ->where(array('position' => $position, 'expire > ?' => time()))
                ->limit($count);
            $result = $this->selectWith($select)->toArray();
            return $result;
        }
        return '';
    }

    public function getCountBannerPosition($position = array())
    {
        $data = array();
        $select = $this->getSql()->select();
        $select->columns(array('position', 'id' => new \Zend\Db\Sql\Expression('COUNT(id)')));
        $select->where(array('position' => $position, 'expire > ?' => time()));
        $select->group('position');
        $result = $this->selectWith($select);

        if ($result->count() > 0)
            foreach ($result as $row)
                $data[$row->position] = $row->id;
        else
            foreach ($position as $val)
                $data[$val] = 0;
        return $data;
    }

    public function getFirstExpireDateGroup($position = array())
    {
        $dataArray = array();
        if ($position) {

            $platform = $this->getAdapter()->getPlatform();
            $table = $platform->quoteIdentifier($this->table);
            $positions = $platform->quoteIdentifier('position');
            $expire = $platform->quoteIdentifier('expire');
            $time = time();

            $q = null;
            foreach ($position as $val) {
                $select = sprintf("(SELECT * from %s WHERE %s='%s' AND %s > %s ORDER BY %s DESC LIMIT 1)",
                    $table, $positions, $val, $expire , $time , $expire
                );
                if (!$q)
                    $q = $select;
                else
                    $q .= 'UNION ' . $select;
            }

            $result = $this->getAdapter()->query($q)->execute();
            if ($result)
                foreach ($result as $row)
                    $dataArray[$row['position']] = $row['expire'];
        }
        return $dataArray;
    }

    public function  getExpired($type = 0)
    {
        if ($type == 1) {
            $threeDay = strtotime('- 3 days');
            $twoDay = strtotime('- 2 days');
            return $this->getAll(array('expire > ?' => $threeDay, 'expire < ?' => $twoDay));
        }
    }

    public function getBannerPosition()
    {
        $select = $this->getSql()->select();
        $select->columns(array('position' => new \Zend\Db\Sql\Expression('DISTINCT(position)')));
        $result = $this->selectWith($select)->toArray();
        return $result;
    }

    public function getBannerWithSiteNameBlock()
    {
        $arrayId = array();
        $select = $this->getall(array('type' => 'banner_block', 'enabled' => 1));
        foreach ($select as $row) {
            $data = unserialize($row->data);
            if (isset($data['banner_block']['site']) && !empty($data['banner_block']['site']))
                $arrayId[] = $row->id;
        }
        $otherSelect = $this->getAll(array('type' => array('menu_block', 'user_login_block')));
        foreach ($otherSelect as $row)
            $arrayId[] = $row->id;
        return $this->getAll(array('id' => $arrayId));
    }
}
