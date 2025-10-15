<?php
namespace Gallery\Model;

use System\DB\BaseTableGateway;
use Zend\Db\Sql\Select;

class BannerSizeTable extends BaseTableGateway
{
    protected $table = 'tbl_banner_size';
    protected $model = 'Gallery\Model\BannerSize';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getArray()
    {
        $dataArray = array();
        $select = $this->getAll(array('status' => 1));
        if ($select)
            foreach ($select as $row) {
                $dataArray[$row->id] = array(
                    'id' => $row->id,
                    'position' => $row->position,
                    'viewPosition' => $row->position . ' (' . $row->width . '*' . $row->height . ')',
                    'price' => $row->price,
                    'addPrice' => $row->addPrice
                );
            }
        return $dataArray;
    }

    public function getPosition()
    {
        $dataArray = array();
        $select = $this->getSql()->select();
        $select->columns(array('position' => new \Zend\Db\Sql\Expression('DISTINCT(position)')));
        $select->where(array('status' => 1));
        $result = $this->selectWith($select)->toArray();
        if ($result)
            foreach ($result as $row)
                $dataArray[] = $row['position'];
        return $dataArray;
    }

    public function getViewPosition()
    {
        $dataArray = array();
        $select = $this->getAll(array('status' => 1));
        if ($select)
            foreach ($select as $row) {
                $dataArray[$row->id] = $row->position . ' (' . $row->width . '*' . $row->height . ')';
            }
        return $dataArray;
    }

    public function getSize($id)
    {
        $dataArray = array();
        if ($id) {
            $select = $this->get($id);
            if ($select) {
                if ($select->width)
                    $dataArray['width'] = $select->width;
                else
                    $dataArray['width'] = 50;
                if ($select->height)
                    $dataArray['height'] = $select->height;
                else
                    $dataArray['height'] = 50;
            }
        }
        return $dataArray;
    }

    public function getPositionById($id)
    {
        /** @var $select BannerSize */
        $positionName = '';
        if ($id) {
            $select = $this->get($id);
            if ($select->position)
                $positionName = $select->position;
        }
        return $positionName;
    }
}
