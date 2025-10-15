<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 4/7/14
 * Time: 2:05 PM
 */

namespace Application\API;


use System\API\BaseAPI;

class Help extends BaseAPI
{
    const LOAD_HELP_LINKS = 'load_help_links';

    public $flatPages = array();

    public function loadHelp()
    {
        $cacheKey = "help_page_links";
        $flatCacheKey = 'help_page_links_flatten';
        if ($helpPages = getCacheItem($cacheKey)) {
            $this->flatPages = getCacheItem($flatCacheKey);
            return $helpPages;
        }

        $helpPages = array();
        $modules = getSM('ModuleManager')->getModules();
        foreach ($modules as $m) {
            $helpConfig = ROOT . '/module/' . $m . '/config/help.config.php';
            if (file_exists($helpConfig)) {
                $help = include $helpConfig;
                $helpPages = array_merge($helpPages, $help);
            }
        }

        setCacheItem($cacheKey, $helpPages);
        if (!$this->flatPages = getCacheItem($flatCacheKey)) {
            $this->flatten($helpPages);
            setCacheItem($flatCacheKey, $this->flatPages);
        }

        return $helpPages;
    }

    private function flatten($pages)
    {
        foreach ($pages as $name => $page) {
            $subPages = array();
            if (isset($page['pages'])) {
                $subPages = $page['pages'];
                unset($page['pages']);
            }
            $this->flatPages[$name] = $page;
            if (count($subPages))
                $this->flatten($subPages);
        }
    }
} 