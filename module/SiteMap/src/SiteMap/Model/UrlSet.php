<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/14/13
 * Time: 1:45 PM
 */

namespace SiteMap\Model;


use Theme\API\Common;

class UrlSet
{
    private $urls = array();
    public $tree = array();

    public function addUrl(Url $url)
    {
        $this->urls[] = $url;
    }

    public function toXml()
    {
        $xml = array();
        /* @var $url Url */
        foreach ($this->urls as $url) {
            $xml[] = $url->toXml();
        }
        $xml = implode("\n", $xml);

        $urlset = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">%s</urlset>';
        $urlset = sprintf($urlset, $xml);
        return $urlset;
    }

    public function getTree()
    {
        $this->tree['/']['data'] = Common::Link(t('Home'), url('app/front-page'));
        return $this->tree;
    }


} 