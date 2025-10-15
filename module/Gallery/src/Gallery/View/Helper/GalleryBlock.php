<?php
namespace Gallery\View\Helper;

use Application\API\App;
use Components\API\Block;
use System\View\Helper\BaseHelper;

class GalleryBlock extends BaseHelper
{
    public function __invoke($block)
    {

        $site = false;
        if (isset($block->data[$block->type]['groupId']))
            $groupId = $block->data[$block->type]['groupId'];
        $type = $block->data[$block->type]['type'];
        if (isset($block->data[$block->type]['site']))
            $site = $block->data[$block->type]['site'];

        $galleryName = 'gallery-block-' . $block->id;
        $block->data['class'] .= ' gallery-block';
        $block->blockId = $galleryName;


        if ($type == 'gallery') {
            $gallery = getSM('gallery_table')->getAll(array('status' => 1, 'type' => 'gallery', 'id' => $groupId));
            if (count($groupId) == 1) {
                $gallery = $gallery->current();
                $galleryItem = getSM('gallery_item_table')->getAll(array('groupId' => $groupId, 'status' => 1));
                return $this->view->render('gallery/gallery-page/photo-gallery', array(
                    'galleryItem' => $galleryItem,
                    'id' => $groupId[0],
                    'gallery' => $gallery,
                    'hitsType' => 'web',
                ));
            } else {
                return $this->view->render('gallery/gallery-page/index', array(
                    'gallery' => $gallery,
                ));
            }
        }

        if ($type == 'slider') {

            $sliderType = 1;
            if (isset($block->data[$block->type]['sliderType']))
                $sliderType = $block->data[$block->type]['sliderType'];
            $select = getSM()->get('gallery_item_table')->getAll(array('groupId' => $groupId, 'type' => $type, 'status' => 1),array('order DESC'));
            $selectGallery = getSM()->get('gallery_table')->get($groupId);
            if ($selectGallery)
                $selectGallery->config = unserialize($selectGallery->config);
            if ($sliderType == 1) {
                return $this->view->render('gallery/gallery/slider', array(
                    'select' => $select,
                    'selectGallery' => $selectGallery,
                    'hitsType' => 'web',
                ));
            } elseif ($sliderType == 2) {

                return $this->view->render('gallery/gallery/slider-flyout', array(
                    'select' => $select,
                    'selectGallery' => $selectGallery,
                    'hitsType' => 'web',
                ));
            }
        }

        if ($type == 'imageBox') {
            $select = getSM()->get('gallery_item_table')->getAll(array('groupId' => $groupId, 'type' => $type, 'status' => 1));
            $selectGallery = getSM()->get('gallery_table')->get($groupId);
            if ($selectGallery)
                $selectGallery->config = unserialize($selectGallery->config);
            return $this->view->render('gallery/gallery/image-box', array(
                'select' => $select,
                'selectGallery' => $selectGallery,
                'hitsType' => 'web',
            ));
        }

        if ($type == 'banner') {
            // $activeSite = App::siteUrl(); // TODO subdomain ha ham banner site asli ra begirand
            /*if (!empty($site) && $site != $activeSite) {
                if (!$html = getCache()->getItem('banner_image_' . $groupId))
                    $html = '';
                return $html;
            } else {*/
            $select = null;
            $url = url('app/banner-loader');
            $cache_key = 'banner_name_' . $block->id;
            if (cacheExist($cache_key))
                $dataArray = getCacheItem($cache_key);
            else {
                $config = getSM('config_table')->getByVarName('banner_config')->varValue;
                $count = 3;
                if (isset($config['countPosition'][$block->position]))
                    $count = (int)$config['countPosition'][$block->position];
                $count++;
                $groupIdArray = getSM('banner_table')->getGroupIdArray($block->position, $count);
                if ($groupIdArray) {
                    $select = getSM('gallery_item_table')->getGroupIdRandom($groupIdArray); //random rooz be rooz ast
                }
                $dataArray = array();
                foreach ($select as $row)
                    $dataArray[] = $row;
                setCacheItem($cache_key, $dataArray);
            }

            return $this->view->render('gallery/gallery/banner', array(
                'select' => $dataArray,
                'url' => $url,
                'hitsType' => 'web',
            ));
            // }
        }
    }
}