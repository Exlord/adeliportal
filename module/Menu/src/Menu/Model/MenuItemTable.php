<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Menu\Model;

use Application\API\App;
use System\API\BaseAPI;
use System\DB\BaseTableGateway;
use Theme\API\Common;
use Zend\Db;
use Zend\Db\TableGateway;

class MenuItemTable extends BaseTableGateway
{
    protected $table = 'tbl_menu_item';
    protected $model = 'Menu\Model\MenuItem';
    protected $caches = array('all_menu_items_sitemap');
    protected $cache_prefix = null;
    protected $translateEntityType = 'menu_item';

    public function getSimpleItems($menuId)
    {
        $select = $this->getSql()->select();
        getSM('translation_api')->translate($select, 'menu_item');
        $select->where(array('status' => 1, 'menuId' => $menuId))
            ->order(array('itemOrder DESC', 'itemTitle ASC'));
//    var_dump($select->getSqlString());
//        die();
        return $this->selectWith($select);
    }

    public function getArray($menuId)
    {
        $data = $this->getAll(array('status' => 1, 'menuId' => $menuId), array('itemOrder DESC', 'itemTitle ASC'));
        if ($data->count()) {
            $data = $this->toNestedArray($data);
            foreach ($data as $key => $value) {
                $data[$key] = str_repeat('|--', $value->indent) . ' ' . $value->itemName;
            }
            return $data;
        }
        return array();
    }

    public function getItems($menuId)
    {
        $data = $this->getAll(array('status' => 1, 'menuId' => $menuId), array('itemOrder DESC', 'itemTitle ASC'));
        if ($data->count()) {
            $data = $this->toNestedArray($data);
            return $data;
        }
        return array();
    }

    public function save(\Menu\Model\MenuItem $model)
    {
        $itemUrlType = $model->itemUrlType;
        $model->itemUrlTypeParams = serialize(array($itemUrlType => $model->itemUrlTypeParams[$itemUrlType]));
        $model->config = serialize($model->config);
        parent::save($model);
    }

    public function get($id)
    {
        $item = parent::get($id);
        if ($item) {
            if (!empty($item->itemUrlTypeParams))
                $item->itemUrlTypeParams = unserialize($item->itemUrlTypeParams);
            if (!empty($item->config))
                $item->config = unserialize($item->config);
        }
        return $item;
    }

    public function remove($id)
    {
        if (!is_array($id))
            $id = (array)$id;
        foreach ($id as $val) {
            $children = $this->getChildren($val);
            parent::remove(array_keys($children));
        }
    }

    public function removeByMenuId($menuId)
    {
        $this->delete(array('menuId' => $menuId));
    }

    public function getMenuItemForSitemap()
    {
        $cacheKey = 'all_menu_items_sitemap';
        if (!$parents = getCacheItem($cacheKey)) {
            $select = $this->getSql()->select();
            getSM('translation_api')->translate($select, 'menu_item');
            $select->where(array('status' => 1))
                ->order(array('parentId ASC'));
            $catItems = $this->selectWith($select);

          //  $items = array();

            if ($catItems && $catItems->count()) {

                $parents = array();
                foreach ($catItems as $item) {
                    $parents[$item->parentId][] = $item;
                }

               // $this->sortMenuItemForSiteMap($parents, 0, $items);

            }
            setCacheItem($cacheKey, $parents);
        }
        return $parents;
    }

    public function sortMenuItemForSiteMap(&$parents, $pId, &$items)
    {
        if (isset($parents[$pId])) {
            foreach ($parents[$pId] as $item) {
                if (isset($item->itemUrlTypeParams)) {
                    /* @var $api BaseAPI */
                    $api = 'Menu\API\Menu';
                    if (isset($item->itemUrlTypeParams[$item->itemUrlType]['api']))
                        $api = $item->itemUrlTypeParams[$item->itemUrlType]['api'];
                    $params = unserialize($item->itemUrlTypeParams);
                    $page = $api::makeMenuUrl($params);
                   // var_dump($page);
                    $label = $item->itemName;
                    $link = Common::Link($label, App::siteUrl() .$page->getHref());
                    $items[$item->id]['data'] = $link;
                }
                //$items[$item->id]['data'] = $item->itemName;
                $this->sortMenuItemForSiteMap($parents, $item->id, $items[$item->id]['children']);
            }
        }
    }


}

