<?php

namespace Contact\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class ContactUserTable extends BaseTableGateway
{
    protected $table = 'tbl_contact_user';
    protected $model = 'Contact\Model\ContactUser';
    protected $caches = array('contact_all_user');
    protected $cache_prefix = null;

    public function getArray()
    {
        $allUser = array();
        $cache_key = 'contact_all_user';
        if (!$allUser = getCacheItem($cache_key)) {
            $select = $this->getAll(array('status' => 1));
            if ($select) {
                foreach ($select as $row)
                    $allUser[$row->id] = $row->name;
                setCacheItem($cache_key, $allUser);
            }

        }
        return $allUser;
    }

    public function getUserContactByParentId($parentId)
    {
        $selectCategory = getSM('category_table')->getByMachineName('contact');
        if ($selectCategory) {
            $data = getSM('category_item_table')->getItems($selectCategory->id, $parentId);
            if ($data)
                $userIdArray = array_keys($data);
            $userIdArray[] = $parentId;
            return $this->getContacts($userIdArray);
        } else
            return '';
    }

    public function search($term)
    {
        $where = array('status' => 1);
        $where['name like ?'] = '%' . $term . '%';

        return $this->getAll($where, array('name ASC'));
    }

    public function searchCategoryList($term)
    {
        $selectCategory = getSM('category_table')->getByMachineName('contact');
        if ($selectCategory) {
            $where = array('itemStatus' => 1, 'catId' => $selectCategory->id);
            $where['itemName like ?'] = '%' . $term . '%';

            return getSM('category_item_table')->getAll($where, array('itemName ASC'));
        } else
            return '';
    }

    public function getContacts($id = null, $catId = null, $type = array(0, 2))
    {
        $select = $this->getSql()->select();
        getSM('translation_api')->translate($select, 'contact');
        $where = array('status' => 1, 'type' => $type);
        if ($id)
            $where['id'] = $id;
        if ($catId)
            $where['catId'] = $catId;

        $select->where($where);
        return $this->selectWith($select);
    }

}
