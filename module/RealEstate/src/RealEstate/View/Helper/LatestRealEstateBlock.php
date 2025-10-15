<?php
namespace RealEstate\View\Helper;


use System\View\Helper\BaseHelper;

class LatestRealEstateBlock extends BaseHelper
{
    private $cssJsLoaded = true;

    public function __invoke($block)
    {
        $count = 5;
        $estate_reg_type = array();
        $estate_type = array();

        if (isset($block->data[$block->type]['count']))
            $count = $block->data[$block->type]['count'];
        if (isset($block->data[$block->type]['estate_type']) && $block->data[$block->type]['estate_type'])
            $estate_type = $block->data[$block->type]['estate_type'];
        if (isset($block->data[$block->type]['estate_reg_type']) && $block->data[$block->type]['estate_reg_type'])
            $estate_reg_type = $block->data[$block->type]['estate_reg_type'];

        $realEstateName = 'latest-real-estate-block-' . $block->id;
            $block->data['class'] .= ' latest-real-estate-block';
        $block->blockId = $realEstateName;

        /*$cache_key = 'latest_real_estate_type_name_' . $block->id;
        if (cacheExist($cache_key))
            $estateTypeName = getCacheItem($cache_key);
        else {*/
            $estateTypeName = getSM('category_item_table')->getArrayName($estate_type);
            /*setCacheItem($cache_key, $estateTypeName);
        }*/
        return $this->view->render('real-estate/helper/latest-real-estate', array(
            'estateTypeName' => $estateTypeName,
            'estateRegType' => $estate_reg_type,
            'count' => $count,
        ));
    }

}
