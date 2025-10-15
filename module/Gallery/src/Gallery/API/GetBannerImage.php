<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/3/13
 * Time: 3:50 PM
 */
namespace Gallery\API;
use Application\API\App;
use System\API\BaseAPI;

class GetBannerImage extends BaseAPI
{
    /**
     * @param $groupId : int
     * @param $type : 'banner' or 'gallery' or 'slider' or 'imageBox'
     * @param $displayType : 1=>'random' or 2=>'order'
     * @return string : one row from gallery_item_table
     */
    public function getBannerImage($groupId,$type,$displayType)
    {
        if ($displayType) {
            switch ($displayType) {
                case 1 :
                    $select = getSM()->get('gallery_item_table')->randomItem($groupId, $type);
                    break;
                case 2 :
                    $session = App::getSession('bannerLoader');
                    $bannerLoader = $session->items;
                    $sessionName = $type . "_" . $groupId;
                    $sessionNameMaxId = $type . "_" . $groupId . "_maxId";
                    if (isset($bannerLoader[$sessionName]) && $bannerLoader[$sessionName]) {
                        $id = $session->items[$sessionName];
                        $maxId = $session->items[$sessionNameMaxId];
                        $select = getSM()->get('gallery_item_table')->getOrderItem($groupId, $type, $id, $maxId);
                        $session->items[$sessionName] = $select->id;
                    } else {
                        $select = getSM()->get('gallery_item_table')->getOrderItem($groupId, $type);
                        $session->items = array();
                        $session->items[$sessionName] = $select->id;
                        $session->items[$sessionNameMaxId] = $select->maxId;
                    }
                    break;
            }
            return $select;
        }
        else
            return '';
    }
}