<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace RSS\API;

use System\API\BaseAPI;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\View\Model\ViewModel;

class Reader extends BaseAPI
{
    public static function read(\RSS\Model\Reader $reader)
    {
        if ($reader->url && strpos($reader->url, 'http') > -1) {
            //$cacheId = 'Zend_Feed_Reader_' . md5($uri);
            \Zend\Feed\Reader\Reader::setCache(getCache());
            \Zend\Feed\Reader\Reader::useHttpConditionalGet();
            $feed = \Zend\Feed\Reader\Reader::import($reader->url);
            if ($feed) {
                $data = array(
                    'title' => $feed->getTitle(),
                    'link' => $feed->getLink(),
                    'dateModified' => $feed->getDateModified(),
                    'description' => $feed->getDescription(),
                    'language' => $feed->getLanguage(),
                    'entries' => array(),
                );

                $i = 1;
                foreach ($feed as $entry) {
                    if ($i <= $reader->feedLimit) {
                        $edata = array(
                            'title' => $entry->getTitle(),
                            'description' => $entry->getDescription(),
                            'dateModified' => $entry->getDateModified(),
                            'authors' => $entry->getAuthors(),
                            'link' => $entry->getLink(),
                            'content' => $entry->getContent()
                        );
                        $data['entries'][] = $edata;
                        $i++;
                    } else
                        break;
                }
                $viewModel = new ViewModel(array('data' => $data));
                $viewModel->setTemplate('rss/reader/feed');
                $viewModel->setTerminal(true);
                $html = self::render($viewModel);
                $cacheKey = 'rss_feed_data_' . $reader->id;
                setCacheItem($cacheKey, $html);
                $reader->lastRead = time();
                getSM('rss_reader_table')->save($reader);
                return $html;
            }
        }
        return false;
    }
}