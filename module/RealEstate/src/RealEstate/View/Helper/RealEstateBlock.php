<?php
namespace RealEstate\View\Helper;


use System\View\Helper\BaseHelper;

class RealEstateBlock extends BaseHelper
{
    private $assetsLoaded = false;
    const DIRECTIONAL_NAV_SHOW = '1';
    const DIRECTIONAL_NAV_HIDE = '2';

    public function __invoke($block)
    {
        $showLoading = true;
        $imageWidth = 50;
        $imageHeight = 50;
        $count = 5;
        $estate_reg_type = array();
        $estate_type = array();
        $view_type = 1;
        $orientation = 'vertical';
        $direction = 'right';
        $interval = 3000;
        $speed = 500;
        $autoScroll = 'yes';
        $directional_nav = self::DIRECTIONAL_NAV_SHOW;
        $visible_count = 0;
        $slide_count = 1;


        if (isset($block->data[$block->type]['count']))
            $count = $block->data[$block->type]['count'];
        if (isset($block->data[$block->type]['showLoading']) && $block->data[$block->type]['showLoading'])
            $showLoading = $block->data[$block->type]['showLoading'];
        if (isset($block->data[$block->type]['imageWidth']) && $block->data[$block->type]['imageWidth'])
            $imageWidth = $block->data[$block->type]['imageWidth'];

        if (isset($block->data[$block->type]['imageHeight']) && $block->data[$block->type]['imageHeight'])
            $imageHeight = $block->data[$block->type]['imageHeight'];
        if (isset($block->data[$block->type]['estate_type']) && $block->data[$block->type]['estate_type'])
            $estate_type = $block->data[$block->type]['estate_type'];
        if (isset($block->data[$block->type]['view_type']) && $block->data[$block->type]['view_type'])
            $view_type = $block->data[$block->type]['view_type'];
        if (isset($block->data[$block->type]['estate_reg_type']) && $block->data[$block->type]['estate_reg_type'])
            $estate_reg_type = $block->data[$block->type]['estate_reg_type'];
        $contentLength = isset($block->data[$block->type]['textLength']) ? (int)$block->data[$block->type]['textLength'] : 0;
        if (isset($block->data[$block->type]['slider'])) {
            $slider = $block->data[$block->type]['slider'];

            if (isset($slider['orientation']) && ($slider['orientation'] == 'vertical' || $slider['orientation'] == 'horizontal'))
                $orientation = $slider['orientation'];

            if (isset($slider['direction']) &&
                ($slider['direction'] == 'right' || $slider['direction'] == 'left' || $slider['direction'] == 'top' || $slider['direction'] == 'bottom')
            )
                $direction = $slider['direction'];

            if (isset($slider['autoScroll']) && ($slider['autoScroll'] == 'yes' || $slider['autoScroll'] == 'no'))
                $autoScroll = $slider['autoScroll'];

            if (isset($slider['interval'])) {
                $_interval = (int)$slider['interval'];
                if ($_interval)
                    $interval = $_interval;
            }

            if (isset($slider['speed'])) {
                $_speed = (int)$slider['speed'];
                if ($_speed)
                    $speed = $_speed;
            }

            if (isset($slider['directional_nav']))
                $directional_nav = $slider['directional_nav'];

            if (isset($slider['visible_count']))
                $visible_count = (int)$slider['visible_count'];

            if (isset($slider['slide_count']) && (int)$slider['slide_count'] > 0)
                $slide_count = (int)$slider['slide_count'];

            if ($slide_count > $visible_count && $visible_count)
                $slide_count = $visible_count;
        }

        $specialRealEstateName = 'estate-' . $block->id;
        $block->data['class'] .= ' estate-block';
        $block->blockId = $specialRealEstateName;

        $cache_key = 'real_estate_name_' . $view_type . $block->id;
        if (cacheExist($cache_key))
            $realEstate = getCacheItem($cache_key);
        else {
            $realEstate = getSM('real_estate_table')->getRealEstateArrayForList($estate_type, $estate_reg_type, $count, $view_type);
            setCacheItem($cache_key, $realEstate);
        }
        if (!$this->assetsLoaded) {
            $this->assetsLoaded = true;
            $this->view->headLink()->appendStylesheet($this->view->basePath() . '/css/special-list.css');
            $this->view->headLink()->appendStylesheet($this->view->basePath() . '/content-slider/slider.css');
            $this->view->headScript()->appendFile($this->view->basePath() . '/content-slider/slider.js');
        }
        return $this->view->render('real-estate/helper/real-estate', array(
            'realEstate' => $realEstate,
            'orientation' => $orientation,
            'blockId' => $block->id,
            'direction' => $direction,
            'autoScroll' => $autoScroll,
            'interval' => $interval,
            'speed' => $speed,
            'directionalNav' => (int)$directional_nav,
            'visibleCount' => $visible_count,
            'slideCount' => $slide_count,
            'imageWidth' => $imageWidth,
            'imageHeight' => $imageHeight,
            'showLoading' => $showLoading,
        ));
    }

}
