<?php
namespace Page\View\Helper;


use System\View\Helper\BaseHelper;

class ContentBlock extends BaseHelper
{
    const DIRECTIONAL_NAV_SHOW = '1';
    const DIRECTIONAL_NAV_HIDE = '2';
    const DIRECTIONAL_NAV_ALWAYS_SHOW = '3';
    private $assetsLoaded = false;

    public function __invoke($block)
    {
        $count = 5;
        $viewType = 'normal';
        $titleType = 'normal';
        $showTitle = true;
        $showLoading = true;
        $showImage = 0;
        $showPublished = 0;
        $showHits = 0;
        $imageWidth = 50;
        $imageHeight = 50;
//        $visibleCount = 2;
//        $scrollingItems = 1;
        $resizeType = 'fix';

        $orientation = 'vertical';
        $direction = 'right';
        $interval = 3000;
        $speed = 500;
        $autoScroll = 'yes';
        $directional_nav = self::DIRECTIONAL_NAV_SHOW;
        $visible_count = 0;
        $slide_count = 1;
        $customType = 0; //default content
        $externalLink = null;
        $readMore = 0;

        $type = $block->data[$block->type]['type'];
        $tagId = $block->data[$block->type]['tagId'];


        if (isset($block->data[$block->type]['count']))
            $count = $block->data[$block->type]['count'];
        /*if (isset($theme['width']) && $theme['width'])
            $width = (int)$theme['width'];
        if (isset($theme['height']) && $theme['height'])
            $height = (int)$theme['height'];*/
        if (isset($block->data[$block->type]['showLoading']) && $block->data[$block->type]['showLoading'])
            $showLoading = $block->data[$block->type]['showLoading'];

        if (isset($block->data[$block->type]['showPublished']) && $block->data[$block->type]['showPublished'])
            $showPublished = $block->data[$block->type]['showPublished'];

        if (isset($block->data[$block->type]['showHits']) && $block->data[$block->type]['showHits'])
            $showHits = $block->data[$block->type]['showHits'];

        if (isset($block->data[$block->type]['customType']) && $block->data[$block->type]['customType'])
            $customType = (int)$block->data[$block->type]['customType'];

        if (isset($block->data[$block->type]['viewType']) && $block->data[$block->type]['viewType'])
            $viewType = $block->data[$block->type]['viewType'];

        if (isset($block->data[$block->type]['imageWidth']) && $block->data[$block->type]['imageWidth'])
            $imageWidth = $block->data[$block->type]['imageWidth'];

        if (isset($block->data[$block->type]['imageHeight']) && $block->data[$block->type]['imageHeight'])
            $imageHeight = $block->data[$block->type]['imageHeight'];

//        if (isset($block->data[$block->type]['visibleCount']) && $block->data[$block->type]['visibleCount'])
//            $visibleCount = $block->data[$block->type]['visibleCount'];

        if (isset($block->data[$block->type]['resizeType']) && $block->data[$block->type]['resizeType'])
            $resizeType = $block->data[$block->type]['resizeType'];

//        if (isset($block->data[$block->type]['scrollingItems']) && $block->data[$block->type]['scrollingItems'])
//            $scrollingItems = $block->data[$block->type]['scrollingItems'];

        if (isset($block->data[$block->type]['titleType']) && $block->data[$block->type]['titleType'])
            $titleType = $block->data[$block->type]['titleType'];
        if ($titleType == 'titleHidden')
            $showTitle = false;

        if (isset($block->data[$block->type]['externalLink']) && $block->data[$block->type]['externalLink']) {
            $externalLink = trim($block->data[$block->type]['externalLink']);
            if (strpos($externalLink, 'http') === false)
                $externalLink = 'http://' . $externalLink;
        }

        if (isset($block->data[$block->type]['readMore']) && $block->data[$block->type]['readMore'])
            $readMore = (int)($block->data[$block->type]['readMore']);


        if (isset($block->data[$block->type]['showImage']) && $block->data[$block->type]['showImage'])
            $showImage = $block->data[$block->type]['showImage'];

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

        $contentName = 'latest-content-block-' . $block->id;
        $block->data['class'] .= ' latest-content-block';
        $block->blockId = $contentName;


        $contentLength = isset($block->data[$block->type]['textLength']) ? (int)$block->data[$block->type]['textLength'] : 0;
        $responsiveImage = isset($block->data[$block->type]['responsiveImage']) ? (int)$block->data[$block->type]['responsiveImage'] : 1;


        $data = array();
        $cache_key = 'latest_content_list_' . $block->id . SYSTEM_LANG;
        if (!$data = getCacheItem($cache_key)) {
            $select = getSM('page_table')->getContent($tagId, $count, $customType);
            if (!$select || !$select->count())
                return t('No Content available');
            //get first images url
            foreach ($select as $row) {
                $link = null;
                $textForImage = $row['fullText'];
                $title = $row['pageTitle'];
                if (strlen($title) > 150)
                    $title = mb_substr($title, 0, 150, 'UTF-8');
                $introText = '';
                if ($row['introText'])
                    $introText = strip_tags($row['introText']);
                if ($customType == 2)
                    $link = getSM('page_api')->getUrlFromText($row['fullText']);
                $text = false;
                if ($contentLength) {
                    $fullText = strip_tags($row['fullText']);
                    $fullTextLength = mb_strlen($fullText, 'UTF-8');
                    if ($fullTextLength > $contentLength)
                        $text = mb_substr($fullText, 0, $contentLength, 'UTF-8') . ' ...';
                    else
                        $text = $fullText;
                }
                $hits = 0;
                if (isset($row['hits']))
                    $hits = $row['hits'];

                $data[$row['id']] = array(
                    'id' => $row['id'],
                    'title' => $title,
                    'text' => $text,
                    'introText' => $introText,
                    'publishUp' => $row['publishUp'],
                    'link' => $link,
                    'hits' => $hits,
                );
                if ($showImage) {
                    //  $imageUrl = getSM('page_api')->getImageFromText($row['fullText']);
                    if (isset($row['image']) && $row['image']) {
                        $baseImage = unserialize($row['image']);
                        if (isset($baseImage['image']) && $baseImage['image']) {
                            if ($imageHeight && $imageWidth) {
                                if ($resizeType == 'fix')
                                    $baseImage['image'] = getThumbnail()->resize($baseImage['image'], $imageWidth, $imageHeight); //resize image
                                elseif ($resizeType == 'relative')
                                    $baseImage['image'] = getThumbnail()->thumbnail($baseImage['image'], $imageWidth, $imageHeight); //resize image
                            }
                            $data[$row['id']]['baseImage'] = $baseImage;
                            $textImage = getSM('page_api')->getImageFromText($textForImage);
                            if ($textImage) {
                                $data[$row['id']]['secondImage']['image'] = getThumbnail()->resize($textImage, $imageWidth, $imageHeight);
                            }
                        } else {
                            $textImage = getSM('page_api')->getImageFromText($textForImage);
                            if ($textImage) {
                                $data[$row['id']]['baseImage']['image'] = getThumbnail()->resize($textImage, $imageWidth, $imageHeight);
                            }

                        }
                    }
                }
            }
            if (!$customType)
                setCacheItem($cache_key, $data);
        }

        $block->data['class'] .= ' content-block';
        $block->blockId = 'content-block-' . $block->id;

        //baraye inke css va js faghat yek bar load shavad

        if (!$this->assetsLoaded && $viewType == 'slider') {
            $this->assetsLoaded = true;
            $this->view->headLink()->appendStylesheet($this->view->basePath() . '/content-slider/slider.css');
            $this->view->headScript()->appendFile($this->view->basePath() . '/content-slider/slider.js');
        }
        //end

        $pageUrl = '';
        if ($viewType == 'normal')
            $pageUrl = 'page/helper/content';
        elseif ($viewType == 'slider')
            $pageUrl = 'page/helper/content-slider';

        $output = $this->view->render($pageUrl, array(
            'data' => $data,
            //'theme' => $theme,
            'orientation' => $orientation,
//            'visibleCount' => $visibleCount,
            'imageWidth' => $imageWidth,
            'imageHeight' => $imageHeight,
            'blockId' => $block->id,
//            'scrollingItems' => $scrollingItems,
            'titleType' => $titleType,
            'direction' => $direction,
            'autoScroll' => $autoScroll,
            'interval' => $interval,
            'speed' => $speed,
            'responsiveImage' => $responsiveImage,
            'directionalNav' => (int)$directional_nav,
            'visibleCount' => $visible_count,
            'slideCount' => $slide_count,
            'showTitle' => $showTitle,
            'showLoading' => $showLoading,
            'customType' => $customType,
            'showImage' => $showImage,
            'showPublished' => $showPublished,
            'showHits' => $showHits,
            'externalLink' => $externalLink,
            'readMore' => $readMore,
        ));

        return $output;
    }
}
