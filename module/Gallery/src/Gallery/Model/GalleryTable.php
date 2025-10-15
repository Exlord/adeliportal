<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Gallery\Model;

use System\DB\BaseTableGateway;

class GalleryTable extends BaseTableGateway
{
    protected $table = 'tbl_gallery';
    protected $model = 'Gallery\Model\Gallery';
    protected $caches = array('gallery_groups');
    protected $cache_prefix = null;

    public function getGroupsArray($where = null)
    {
        $data = array();
        $cache_key = 'gallery_groups';
        if (!$data = getCacheItem($cache_key)) {
            $select = $this->getAll($where);
            foreach ($select as $row)
                $data[$row->id] = $row->groupName;
        }
        return $data;
    }

    public function getByName($name)
    {
        return $this->select(array('groupName' => $name))->current();
    }

    public function search($term)
    {
        $where = array(
            'status' => 1,
            'type' => 'gallery',
            'groupName like ?' => '%' . $term . '%',
        );
        return $this->getAll($where, array('groupName ASC'));
    }
}
