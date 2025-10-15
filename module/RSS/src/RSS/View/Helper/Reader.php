<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/12/13
 * Time: 10:15 AM
 */

namespace RSS\View\Helper;


use RSS\Model\ReaderTable;
use System\View\Helper\BaseHelper;

class Reader extends BaseHelper
{
    public function __invoke($block)
    {
        //$cacheId = 'Zend_Feed_Reader_' . md5($uri);
        $html = '';
        $feedId = $block->data['rss_reader_block']['feedId'];
        $blockName = 'feed-block-' . $block->id;
        if (!isset($block->data['class']))
            $block->data['class'] = 'feed-reader-block';
        $block->blockId = $blockName;

        /* @var $feedModel \RSS\Model\Reader */
        $feedModel = getSM('rss_reader_table')->get($feedId);


        if ($feedModel) {
            $cacheKey = 'rss_feed_data_' . $feedModel->id;
            if($feedData = getCacheItem($cacheKey))
                return $feedData;

            $html = \RSS\API\Reader::read($feedModel);
            if($html)
                return $html;
            else
                return '';
        }
        return $html;
    }
}