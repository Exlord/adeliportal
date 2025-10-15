<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace SiteMap\API;

use SiteMap\Model\UrlSet;
use System\API\BaseAPI;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class SiteMap extends BaseAPI
{
    const GENERATING = 'SitemapGeneratingGenerating';
    const CACHE_KEY = "xml_sitemap";
    const CACHE_KEY_ARRAY = "array_sitemap";

    private $config = null;

    public function Generate()
    {
        $config = getConfig('sitemap')->varValue;
        $sendConfig = array();
        if(isset($config['modules']))
            $sendConfig = $config['modules'];
        $sitemap = new UrlSet();
        $this->getEventManager()->trigger(self::GENERATING, $this,
            array(
                'sitemap' => &$sitemap,
                'config' => $sendConfig,
            )
        );
        $tree = $sitemap->getTree();
        setCacheItem($this->getCacheKey(), $tree);
        $sitemap = $sitemap->toXml();
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . $sitemap;
        setCacheItem(self::CACHE_KEY, $xml);
        return $xml;
    }

    public function get()
    {
        if (!$xml = getCacheItem(self::CACHE_KEY))
            $xml = $this->Generate();

        $response = new \Zend\Http\Response();
        $response->getHeaders()->addHeaderLine('Content-Type', 'text/xml; charset=utf-8');
        $response->setContent($xml);
        return $response;
    }

    public function getTree()
    {
        if (!$sitemap = getCacheItem($this->getCacheKey())) {
            $this->Generate();
            $sitemap = getCacheItem($this->getCacheKey());
        }
        return $sitemap;
    }

    private function getCacheKey()
    {
        return self::CACHE_KEY_ARRAY . '_' . SYSTEM_LANG;
    }

    public function getConfig($module = null)
    {
        if (is_null($this->config)) {

            $cacheKey = 'sitemap_merged_config';
            if (!IS_DEVELOPMENT_SERVER)
                $this->config = getCacheItem($cacheKey);

            if (!$this->config) {
                $this->config = array();
                $modules = getSM('ModuleManager')->getModules();
                foreach ($modules as $m) {
                    $f = ROOT . '/module/' . $m . '/config/sitemap.config.php';
                    if (file_exists($f))
                        $this->config = array_merge_recursive($this->config, include $f);
                }

                if (!IS_DEVELOPMENT_SERVER)
                    setCacheItem($cacheKey, $this->config);
            }
        }
        if ($module) {
            if (isset($this->config[$module]))
                return $this->config[$module];
            else
                return null;
        }
        return $this->config;
    }

}