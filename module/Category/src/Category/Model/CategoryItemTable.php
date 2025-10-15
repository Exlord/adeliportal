<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Category\Model;

use Application\API\App;
use System\DB\BaseTableGateway;
use Theme\API\Common;
use Zend\Db;
use Zend\Db\TableGateway;

class CategoryItemTable extends BaseTableGateway
{
    protected $table = 'tbl_category_item';
    protected $model = 'Category\Model\CategoryItem';
    protected $caches = array('category_items', 'all_category_item_array', 'all_item_list_array');
    protected $cache_prefix = array('all_item_list_array_', 'category_parents_', 'category_active_items_tree_', 'item_name_', 'category_items_first_level_', 'all_category_items_');
    protected $translateEntityType = 'category_item';

    private $categoryItems = null;

    public function getItemsTreeByMachineName($name)
    {
        $cache_key = 'category_active_items_tree_' . $name;
        if (!$items = getCache()->getItem($cache_key)) {
            $cat = $this->getServiceLocator()->get('category_table')->getByMachineName($name);
            if ($cat) {
                $items = $this->getItemsTree($cat->id);
                getCache()->setItem($cache_key, $items);
            } else {
                $items = array();
            }
        }
        return $items;
    }

    public function getItemsTree($catId, $parentId = 0)
    {
        $cache_key = 'category_active_items_tree_' . $catId . $parentId;
        if (!$items = getCache()->getItem($cache_key)) {
            $where = array('catId' => $catId);
            $where['itemStatus'] = '1';
            $data = $this->getAll($where, array('parentId ASC', 'itemOrder ASC'));
            $parents = array();
            foreach ($data as $row) {
                $parents[$row->parentId][] = $row;
            }

            $items = array();
            $this->_makeArray($parents, $parentId, 0, $items);

            getCache()->setItem($cache_key, $items);
        }
        return $items; //TODO cache these bitches
    }

    private function _makeArray(&$data, $parent, $indent, &$items)
    {
        if (count($data)) {
            if (isset($data[$parent]))
                foreach ($data[$parent] as $item) {
                    $items[$item->id] = str_repeat('|--', $indent) . $item->itemName;

                    if (isset($data[$item->id])) {
                        $this->_makeArray($data, $item->id, ++$indent, $items);
                        --$indent;
                    }
                }
        }
    }

    public function getParents($parentId)
    {
        $cache_key = 'category_parents_' . $parentId;
        if (!$parents = getCache()->getItem($cache_key)) {
            $parents = array();
            while ($parentId != 0) {
                $p = $this->get($parentId);
                $parents[] = $p;
                $parentId = (int)$p->parentId;
            }
            getCache()->setItem($cache_key, $parents);
        }
        return $parents; //TODO cache these bitches
    }

    public function getChildCount($id)
    {

    }

    public function getItemsByMachineName($name, $parentId = false)
    {
        $cat = $this->getServiceLocator()->get('category_table')->getByMachineName($name);
        if ($cat) {
            $where = array('catId' => $cat->id,);
            $where['itemStatus'] = '1';
            if ($parentId !== false)
                $where['parentId'] = $parentId;
            return $this->getAll($where);
        } else {
            return null;
        }
    }

//    public function getParentsWithTagsId($tagsId)
//    {
//        if (cacheExist('item_name_' . $tagsId))
//            $itemName = getCacheItem('item_name_' . $tagsId);
//        else {
//            $itemName = $this->getParents($tagsId);
//            setCacheItem('item_name_' . $tagsId, $itemName);
//        }
//        return $itemName;
//    }

    public function getItemsFirstLevelByMachineName($name, $parentId = 0)
    {
        $cache_key = 'category_items_first_level_' . $name . '_' . $parentId;
        if (!$items = getCache()->getItem($cache_key)) {
            $cat = $this->getServiceLocator()->get('category_table')->getByMachineName($name);
            if (isset($cat->id)) {
                $items = $this->getAll(array('catId' => $cat->id, 'parentId' => $parentId))->toArray();
//                foreach ($selectChild as $row)
//                    $items[$row->id] = $row->itemName;
                getCache()->setItem($cache_key, $items);
            } else {
                $items = array();
            }
        }
        return $items;
    }

    public function getByName($name)
    {
        return $this->select(array('itemName' => $name))->current();
    }

    public function removeAllByCategoryId($catId)
    {
        $select = $this->getAll(array('catId' => $catId));
        if ($select) {
            $itemId = array();
            foreach ($select as $row)
                $itemId[] = $row->id;
            if (count($itemId)) {
                //TODO delete image
                $this->delete(array('id' => $itemId));
            }
        }
    }

    public function remove($id)
    {
        if (!is_array($id))
            $id = (array)$id;
        foreach ($id as $val) {
            $children = $this->getChildren($val);
            $ids[$val] = array_keys($children);
            getSM('file_table')->removeByEntityType(\Category\Module::ENTITY_TYPE_CATEGORY_ITEM, $ids[$val]);
            parent::remove($ids[$val]);
        }
    }

    /*public function searchContact($term)
    {
        $where = array('status' => 1);
        $where['name like ?'] = '%' . $term . '%';

        return $this->getAll($where, array('name ASC'));
    }*/

    public function getAllItemList($catId, $parentId = 0, $countLevel = 1)
    {
        $cache_key = 'all_item_list_array_' . $catId;
        if (!$result = getCache()->getItem($cache_key)) {
            $counter = 1;
            $parentIds = array();
            while ($countLevel > 0) {
                if ($counter == 1) {
                    if ($parentId)
                        $parentIds[] = $parentId;
                    else
                        $parentIds = 0;
                }
                $select = $this->getAll(array('itemStatus' => 1, 'catId' => $catId, 'parentId' => $parentIds));
                $parentIds = array();
                if ($select->count()) {
                    foreach ($select as $row) {
                        $sql = $this->getSql();
                        $select = $sql->select();
                        $select->columns(array(new Db\Sql\Expression('COUNT(' . $this->table . '.id) AS id')));
                        $select->where(array('itemStatus' => 1, 'catId' => $catId, 'parentId' => $row->id));
                        $countChild = $this->selectWith($select)->current();
                        if (isset($countChild->id))
                            $row->countChild = $countChild->id;
                        else
                            $row->countChild = 0;
                        $result[$counter][] = $row;
                        $parentIds[] = $row->id;
                    }
                }
                $counter++;
                $countLevel--;
            }
            // getCache()->setItem($cache_key, $result);
        }
        return $result;
    }

    public function searchItemList($data)
    {
        $where['itemName like ?'] = '%' . $data['term'] . '%';
        if (isset($data['catId']) && $data['catId'])
            $where['catId'] = $data['catId'];
        $select = $this->getAll($where, array('itemName ASC'));
        $parents = array();
        foreach ($select as $row) {
            $parents[$row->parentId][] = $row;
        }
        $items = array();
        $this->_makeArray($parents, 0, 0, $items);
        return $items;

    }

    public function getAllItemsByCatName($catName)
    {
        $items = array();
        $cache_key = 'category_items';
        if (!$items = getCache()->getItem($cache_key)) {
            $cat = getSM('category_table')->getAll(array('catMachineName' => $catName))->current();
            $catItem = $this->getAll(array('catId' => $cat->id));
            foreach ($catItem as $row)
                $items[$row->id] = $row;
        }
        return $items;
    }

    public function getItemsArrayByCatName($catName)
    {
        $items = array();
        $cache_key = 'category_items_array';
        if (!$items = getCache()->getItem($cache_key)) {
            $cat = getSM('category_table')->getAll(array('catMachineName' => $catName))->current();
            if ($cat) {
                $catItem = $this->getAll(array('catId' => $cat->id));
                if ($catItem) {
                    foreach ($catItem as $row)
                        $items[$row->id] = $row->itemName;
                }
            }
        }
        return $items;
    }

    public function getUrl($catItem)
    {
        return url('app/content', array('tagId' => $catItem->id, 'tagName' => $catItem->itemName));
    }

    public function getArrayName($id)
    {
        //TODO cache me
        $dataArray = array();
        if (is_array($id)) {
            $select = $this->getAll(array('id' => $id, 'itemStatus' => 1));
            if ($select)
                foreach ($select as $row)
                    $dataArray[$row->id] = $row->itemName;
        }
        return $dataArray;
    }

    public function systemSearch($category, $keyword)
    {
        $cat = getSM('category_table')->getAll(array('catMachineName' => $category));
        if (!$cat)
            return null;

        $table = $this->table;
        $cat = $cat->current()->id;

        $where1 = new Db\Sql\Where();
        $where1->equalTo('catId', $cat);

        $select = $this->getSql()->select();
        $tTable = getSM('translation_api')->translate($select, 'category_item');
        //if translation is not being done
        if ($tTable === false) {
        } else {
            //check the translated table for data match
            $table = $tTable;
        }
        $where2 = new Db\Sql\Where();
        $where2->like($table . '.itemName', '%' . $keyword . '%')
            ->or->like($table . '.itemText', '%' . $keyword . '%');

        $select->where->addPredicate($where1)->addPredicate($where2);
        $result = $this->selectWith($select);
        return $result;
    }

    public function getItemsByParentId($parentId = 0, $catId, $itemId = null)
    {
        $where = array('parentId' => $parentId, 'itemStatus' => 1, 'catId' => $catId);
        if ($itemId)
            $where['id'] = $itemId;
        $select = $this->getSql()->select();
        $select->where($where);
        getSM('translation_api')->translate($select, 'category_item');
        return $this->selectWith($select);
    }

    public function getAllArray()
    {
        $items = array();
        $cache_key = 'all_category_item_array';
        if (!$items = getCache()->getItem($cache_key)) {
            $catItem = $this->getAll();
            if ($catItem) {
                foreach ($catItem as $row)
                    $items[$row->id] = $row->itemName;
                getCache()->setItem($cache_key, $items);
            }
        }
        return $items;
    }

    public function getItems($catNameOrCatId, $parentIds = array())
    {
        $cacheKey = @implode('_', $parentIds);
        $cacheKey = 'all_category_items_' . md5($catNameOrCatId . $cacheKey);
        if (!$items = getCacheItem($cacheKey)) {
            //ini_set('memory_limit','512M');
            if (!is_int($catNameOrCatId))
                $catId = getSM('category_table')->getAll(array('catMachineName' => $catNameOrCatId))->current()->id;
            else
                $catId = $catNameOrCatId;

            $select = $this->getSql()->select();
            getSM('translation_api')->translate($select, 'category_item');
            $select->where(array('catId' => $catId))
                ->order(array('parentId ASC'));
            $catItems = $this->selectWith($select);

            $items = array();
            if ($catItems && $catItems->count()) {

                $parents = array();
                foreach ($catItems as $item) {
                    $parents[$item->parentId][] = $item;
                }


                if ($parentIds && is_array($parentIds))
                    foreach ($parentIds as $pId)
                        $this->__filterItemsByParentId($parents, $pId, $items);
            }
            setCacheItem($cacheKey, $items);
        }
        return $items;
    }

    private function __filterItemsByParentId(&$parents, $pId, &$items)
    {
        if (isset($parents[$pId])) {
            foreach ($parents[$pId] as $item) {
                $items[$item->id] = $item;
                $this->__filterItemsByParentId($parents, $item->id, $items);
            }
        }
    }

    public function getById($id)
    {
        $select = $this->getSql()->select();
        getSM('translation_api')->translate($select, 'category_item');
        $select->where(array('id' => $id));
        $result = $this->selectWith($select);
        if ($result && $result->count())
            return $result->current();

        return null;
    }

    public function getItemsForSitemap($catNameOrCatId, $parentIds = array())
    {
        $cacheKey = @implode('_', $parentIds);
        $cacheKey = 'all_category_items_' . md5($catNameOrCatId . $cacheKey);
        if (!$items = getCacheItem($cacheKey)) {
            //ini_set('memory_limit','512M');
            if (!is_int($catNameOrCatId))
                $catId = getSM('category_table')->getAll(array('catMachineName' => $catNameOrCatId))->current()->id;
            else
                $catId = $catNameOrCatId;

            $select = $this->getSql()->select();
            getSM('translation_api')->translate($select, 'category_item');
            $select->where(array('catId' => $catId))
                ->order(array('parentId ASC'));
            $catItems = $this->selectWith($select);

            $items = array();
            if ($catItems && $catItems->count()) {

                $parents = array();
                foreach ($catItems as $item) {
                    $parents[$item->parentId][] = $item;
                }
                $items = $parents;
            }
            setCacheItem($cacheKey, $items);
        }
        return $items;
    }

    public function allOperationSave($catItemList, $entityId, $entityType, $catName)
    {
        if ($catItemList && $entityId && $entityType && $catName) {
            $cat = $this->getServiceLocator()->get('category_table')->getByMachineName($catName);
            if ($cat) {
                $catId = $cat->id;
                $select = $this->getSql()->select();
                $select->columns(array('id', 'itemName'))
                    ->where(array(
                        'catId' => $catId,
                        'itemName' => $catItemList
                    ));
                $catItems = $this->selectWith($select);
                $searchArray = array();
                foreach ($catItems as $row)
                    $searchArray[$row->id] = $row->itemName;
                $dataInsert = array();
                foreach ($catItemList as $val) {
                    if (!in_array($val, $searchArray)) {
                        $dataInsert[] = array(
                            'itemName' => $val,
                            'itemText' => $val,
                            'parentId' => 0,
                            'itemStatus' => 0,
                            'catId' => $catId,
                            'itemOrder' => 0,
                        );
                    }
                }
                $ids = $this->multiSave($dataInsert);
                if (isset($ids['inserts'])) {
                    $catItemId = array_merge(array_keys($searchArray), $ids['inserts']);
                    getSM('entity_relation_table')->saveAll($entityId, $entityType, $catItemId);
                }
            }
        }
    }

    public function search($term, $page = 1, $page_limit = 20)
    {
        $term = '%' . str_replace(' ', '%', $term) . '%';
        $sql = $this->getSql();
        $select = $sql->select();
        $where = new Db\Sql\Where();
        $where->like($this->table . '.itemName', $term);
        $select->where(array($where));
        return $this->getPaginated($select, $sql, $page, $page_limit);
    }
}
