<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/3/13
 * Time: 3:50 PM
 */
namespace Page\API;

use Page\Model\PageTable;
use System\API\BaseAPI;

class Page extends BaseAPI
{
    public function __construct()
    {

    }

//    public static function makeMenuUrl($params)
//    {
//        $type = current(array_keys($params));
//        $params = $params[$type];
//        $page = null;
//        switch ($type) {
//            case 'singlePage':
//                $pageId = $params['pageId'];
//                $page = new \Zend\Navigation\Page\Mvc();
//                $page->setUri(self::getPageTable()->getUrl($pageId));
//                break;
//        }
//        return $page;
//    }

    /**
     * @return PageTable
     */
    public static function getPageTable()
    {
        return getSM('page_table');
    }

    public function contentForNewsletter()
    {
        $config = getSM('config_table')->getByVarName('newsletter')->varValue;
        if (isset($config['tagId']) && $config['tagId']) {
            $tagId = $config['tagId'];
            $count = 10;
            $moduleName = '';
            if (isset($config['count']) && $config['count'])
                $count = $config['count'];

            if (isset($config['moduleName']) && $config['moduleName'])
                $moduleName = $config['moduleName'];

            if ($moduleName) {
                $selectLastId = 0;
                $selectLastPosted = getSM('newsletter_posted_table')->getAll(array('entityType' => $moduleName, 'entityId' => $tagId))->current();
                if ($selectLastPosted)
                    $selectLastId = $selectLastPosted->lastId;
                /*$maxIdContent = getSM('page_tags_table')->getMaxPageId($tagId);
                if ($maxIdContent) {
                    if ($maxIdContent - $selectLastId >= $count)
                    {*/
                $pages = getSM('page_table')->getContent($tagId, $count, $selectLastId);
                $emails = getSm('newsletter_sign_up_table')->getEmails();
                //TODO Create render Code
                /*     }
                 }*/
            }
        }

    }

    public function getImageFromText($text)
    {
        preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/', $text, $image);

        if (isset($image[1]) && $image[1]) {
            if (strpos($image[1], 'http') > -1) {
                //create folder otherHost
                $dir = PUBLIC_FILE . '/otherHost';
                if (!is_dir($dir))
                    mkdir($dir, 0755);
                //end
                //download images to host
                $filenameIn = $image[1];
                $filenameOut = PUBLIC_FILE . '/otherHost/' . basename($image[1]);
                $path = file_get_contents($filenameIn);
                $byte = file_put_contents($filenameOut, $path);
                if ($byte)
                    return PUBLIC_FILE_PATH . '/otherHost/' . basename($image[1]);
                else
                    return null;
                //end
            } else{
                return $image[1];
            }
        } else
            return null;
    }

    public function getUrlFromText($text)
    {
        preg_match('/<a.+href=[\'"]([^\'"]+)[\'"].*>/', $text, $url);
        if (isset($url[1]) && $url[1])
            return $url[1];
        return null;
    }

    public function getCategories()
    {
        $dataArray = array();
        $selectCat = getSM('category_table')->getAll(array('catMachineName' => 'article'))->current();
        if (isset($selectCat->id)) {
            $dataArray = getSM('category_item_table')->getItemsTree($selectCat->id);
        }
        return $dataArray;
    }

    public function getNewsLetter($data)
    {
        $dataArray = false;
        $html = '';
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $dataArray[$key] = getSM('page_table')->getPages($key, 0, false, true, $val);
            }
        }
        if (is_array($dataArray)) {
            foreach ($dataArray as $key => $val)
                $html[$key] = $this->render('page/page/page-newsletter', array('dataArray' => $val));
        }
        return $html;
    }
}