<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Links\Model;

use Theme\API\Common;
use Zend\Db;
use Zend\Db\TableGateway;

class LinksItemTable extends \System\DB\BaseTableGateway
{
    protected $table = 'tbl_links_items';
    protected $model = 'Links\Model\LinksItem';
    protected $caches;
    protected $cache_prefix = null;

    public function createLinkItems($parents, $pId)
    {
        $items = array();
        $this->__sortItemForSiteMap($parents, $pId, $items);
        return $items;
    }

    private function __sortItemForSiteMap(&$parents, $pId, &$items)
    {
        if (isset($parents[$pId])) {
            foreach ($parents[$pId] as $item) {
                if (isset($item->itemName) && $item->itemName)
                    $items[$item->id]['data'] = Common::Link($item->itemName, url('app/links/category', array('catId' => $item->id, 'catName' => $item->itemName), array()));
                $this->__sortItemForSiteMap($parents, $item->id, $items[$item->id]['children']);
            }
        }
    }
}
