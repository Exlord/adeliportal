<?php
namespace Ads\View\Helper;


use System\View\Helper\BaseHelper;

class AdsBlock extends BaseHelper
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
        $baseType = null;
        $secondType = null;
        $starCount = 0;
        $orientation = 'vertical';
        $direction = 'right';
        $interval = 3000;
        $speed = 500;
        $autoScroll = 'yes';
        $directional_nav = self::DIRECTIONAL_NAV_SHOW;
        $visible_count = 0;
        $slide_count = 1;
        $textLength = 150;
        $showImage=0;


        if (isset($block->data[$block->type]['count']))
            $count = $block->data[$block->type]['count'];
        if (isset($block->data[$block->type]['showImage']) && $block->data[$block->type]['showImage'])
            $showImage = $block->data[$block->type]['showImage'];
        if (isset($block->data[$block->type]['showLoading']) && $block->data[$block->type]['showLoading'])
            $showLoading = $block->data[$block->type]['showLoading'];
        if (isset($block->data[$block->type]['textLength']) && $block->data[$block->type]['textLength'])
            $textLength = $block->data[$block->type]['textLength'];
        if (isset($block->data[$block->type]['imageWidth']) && $block->data[$block->type]['imageWidth'])
            $imageWidth = $block->data[$block->type]['imageWidth'];

        if (isset($block->data[$block->type]['imageHeight']) && $block->data[$block->type]['imageHeight'])
            $imageHeight = $block->data[$block->type]['imageHeight'];
        if (isset($block->data[$block->type]['baseType']) && $block->data[$block->type]['baseType'])
            $baseType = $block->data[$block->type]['baseType'];
        if (isset($block->data[$block->type]['secondType']) && $block->data[$block->type]['secondType'])
            $secondType = $block->data[$block->type]['secondType'];
        if (isset($block->data[$block->type]['starCount']) && $block->data[$block->type]['starCount'])
            $starCount = $block->data[$block->type]['starCount'];
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

        $adsName = 'ads-' . $block->id;
        $block->data['class'] .= ' ads-block';
        $block->blockId = $adsName;

        $cache_key = 'ads_name_' . $block->id;
        if (cacheExist($cache_key))
            $adsData = getCacheItem($cache_key);
        else {
            $adsData = getSM('ads_table')->getAdsArrayForList($baseType, $secondType, $starCount, $count);
            setCacheItem($cache_key, $adsData);

        }
        $baseTypeMachineName = null;
        $adsConfig = getSM('ads_api')->loadCache($baseType);
        if (isset($adsConfig['baseTypeMachineName']))
            $baseTypeMachineName = $adsConfig['baseTypeMachineName'];
        if (!$this->assetsLoaded) {
            $this->assetsLoaded = true;
            // $this->view->headLink()->appendStylesheet($this->view->basePath() . '/css/special-list.css');
            $this->view->headLink()->appendStylesheet($this->view->basePath() . '/content-slider/slider.css');
            $this->view->headScript()->appendFile($this->view->basePath() . '/content-slider/slider.js');
        }
        $resolver = getSM('Zend\View\Resolver\TemplatePathStack');
        $template = 'ads/helper/ads-' . $baseType ;
        if (!$resolver->resolve($template))
            $template = 'ads/helper/ads';
        return $this->view->render($template, array(
            'adsData' => $adsData,
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
            'textLength' => $textLength,
            'baseTypeMachineName'=>$baseTypeMachineName,
            'showImage'=>$showImage,
        ));
    }

}
