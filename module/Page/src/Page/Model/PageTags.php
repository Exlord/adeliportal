<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */

/*`id` int(11) NOT NULL AUTO_INCREMENT,
  `pageTitle` varchar(300) DEFAULT NULL,
  `pageContent` text,
  `state` tinyint(1) DEFAULT '0' COMMENT '0=inactive\r\n1=active',
    */
namespace Page\Model;

class PageTags
{

    public $pageId;
    public $tagsId;

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    public function getPageId()
    {
        return $this->pageId;
    }

    public function setTagsId($tagsId)
    {
        $this->tagsId = $tagsId;
    }

    public function getTagsId()
    {
        return $this->tagsId;
    }


}
