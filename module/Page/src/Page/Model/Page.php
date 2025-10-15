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

use System\Model\BaseModel;

class Page extends BaseModel
{
    public $id;
    public $pageTitle;
    public $introText;
    public $fullText;
    public $status;
    public $isStaticPage;
    public $published = 0;
    public $publishUp = null;
    public $publishDown = null;
    public $hits = 0;
    public $createdBy;
    public $config;
    public $tags = array('');
    public $domains;
    public $filters = array('tags', 'domains', 'languages');
    public $image;
    public $order = 0;
    public $refGallery = null;
    public $languages = array('');

    /**
     * @param null $refGallery
     */
    public function setRefGallery($refGallery)
    {
        $this->refGallery = $refGallery;
    }

    /**
     * @return null
     */
    public function getRefGallery()
    {
        return $this->refGallery;
    }
    /**
     * @param mixed $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return mixed
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $domains
     */
    public function setDomains($domains)
    {
        $this->domains = $domains;
    }

    /**
     * @return mixed
     */
    public function getDomains()
    {
        return $this->domains;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setPublished($published)
    {
        $this->published = $published;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublishUp($publishUp)
    {
        $this->publishUp = $publishUp;
    }

    public function getPublishUp()
    {
        return $this->publishUp;
    }

    public function setPublishDown($publishDown)
    {
        $this->publishDown = $publishDown;
    }

    public function getPublishDown()
    {
        return $this->publishDown;
    }

    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    public function setIsStaticPage($isStaticPage)
    {
        $this->isStaticPage = $isStaticPage;
    }

    public function getIsStaticPage()
    {
        return $this->isStaticPage;
    }

    public function setIntroText($introText)
    {
        $this->introText = $introText;
    }

    public function getIntroText()
    {
        return $this->introText;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setHits($hits)
    {
        $this->hits = $hits;
    }

    public function getHits()
    {
        return $this->hits;
    }

    public function setFullText($fullText)
    {
        $this->fullText = $fullText;
    }

    public function getFullText()
    {
        return $this->fullText;
    }

    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }


}
