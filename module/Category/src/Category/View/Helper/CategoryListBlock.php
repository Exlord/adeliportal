<?php
namespace Category\View\Helper;

use System\View\Helper\BaseHelper;
use Theme\API\Common;

class categoryListBlock extends BaseHelper
{
    public function __invoke($block)
    {
        $positionType = 'vertical';
        $titleType = 'normal';
        $imageWidth = 0;
        $imageHeight = 0;
        $countLevel = 1000; //all level
        $resizeType = 'fix';
        //TODO get Config view Type

        $block->data['class'] .= ' category-list-block';
        $block->blockId = 'category-list-block-' . $block->id;
        $type = $block->data[$block->type]['type'];
        $catId = $block->data[$block->type]['catId'];


        if (empty($catId))
            return t('Category is not selected in block setting !');

        $category = getSM('category_table')->get($catId);
        if (!$category)
            return t('Invalid category is selected in block setting !');

        if (isset($block->data[$block->type]['positionType']) && $block->data[$block->type]['positionType'])
            $positionType = $block->data[$block->type]['positionType'];
        if (isset($block->data[$block->type]['imageWidth']) && $block->data[$block->type]['imageWidth'])
            $imageWidth = $block->data[$block->type]['imageWidth'];
        if (isset($block->data[$block->type]['imageHeight']) && $block->data[$block->type]['imageHeight'])
            $imageHeight = $block->data[$block->type]['imageHeight'];
        if (isset($block->data[$block->type]['countLevel']) && $block->data[$block->type]['countLevel'])
            $countLevel = $block->data[$block->type]['countLevel'];
        if (isset($block->data[$block->type]['resizeType']) && $block->data[$block->type]['resizeType'])
            $resizeType = $block->data[$block->type]['resizeType'];
        if (isset($block->data[$block->type]['titleType']) && $block->data[$block->type]['titleType'])
            $titleType = $block->data[$block->type]['titleType'];
        $cache_key = 'category_list_block_item_' . $block->id;

        if (!$data = getCacheItem($cache_key)) {
            $options = array(
                'imageWidth' => $imageWidth,
                'imageHeight' => $imageHeight,
                'resizeType' => $resizeType,
                'titleType' => $titleType,
                'positionType' => $positionType,
                'countLevel' => $countLevel,
            );
            $select = getSM('category_item_table')->getAllItemList($catId,0, $countLevel);

            if (!$select)
                return t("No active item is available in this category");

            $html = getSM('category_item_list_api')->createCatItemList($select, $options, $category, $countLevel,0);
            $data = $this->view->render('category/helper/cat-item-list-block', array(
                'html' => $html
            ));
            setCacheItem($cache_key, $data);
        }
        $this->view->headLink()->appendStylesheet($this->view->basePath() . '/css/category.css');

        return $data;
    }



}
