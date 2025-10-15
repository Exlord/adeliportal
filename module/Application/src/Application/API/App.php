<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/5/13
 * Time: 9:33 AM
 */

namespace Application\API;

use System\API\BaseAPI;
use System\IO\Directory;
use System\IO\File;
use Zend\Cache\Storage\Adapter\Memcached;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Container as SessionContainer;

class App
{
    const ALL_BUT_MATCHED = '0';
    const JUST_MATCHED = '1';

    private static $Session = array();
    private static $siteUrl = null;
    private static $_domainRegexps = array();
    private static $_regexps = array();
    private static $_matchedRoute = null;
//    private static $_globalSystemCache = null;
    private static $_hasIntro = null;

    /**
     * @param null $dbName
     * @param null $dbUser
     * @param null $dbPass
     * @return Adapter
     */
    public static function getDbAdapter($dbName = null, $dbUser = null, $dbPass = null)
    {
        if ($dbName == null)
            return getSM('db_adapter');

        $config = getSM('ApplicationConfig');
        $db = $config['db'];
        $db['database'] = $dbName;
        $db['username'] = $dbUser;
        $db['password'] = $dbPass;

        return new Adapter($db);
    }

    /**
     * @param string $namespace
     * @return \Zend\Session\Container\PhpReferenceCompatibility
     */
    public static function getSession($namespace = 'Default')
    {
        /* @var $container \Zend\Session\Container\PhpReferenceCompatibility */
        $container = null;
        if (isset(self::$Session[$namespace]))
            $container = self::$Session[$namespace];
        else {
            $container = new SessionContainer($namespace);
            self::$Session[$namespace] = $container;
        }
        return $container;
    }

    public static function prepareUrlString($string)
    {
        $string = strip_tags($string);
        $replaces = array('|--', '&', '=', '+', '%', '/', '\\', '?');
        $string = str_replace($replaces, '', $string);
        $string = str_replace(' ', '-', $string);
        $string = mb_substr($string, 0, 200);
        return $string;
    }

    public static function siteUrl()
    {
        if (is_null(self::$siteUrl)) {
            /* @var $request \Zend\Http\PhpEnvironment\Request */
            $request = getSM()->get('Request');
            $uri = $request->getUri();
            $server_url = $uri->getScheme() . '://' . $uri->getHost();

            self::$siteUrl = $server_url;
        }
        return self::$siteUrl;
    }

    public static function setEditor($id, $view = null)
    {
        if ($view) {
            $query = getSM('Request')->getQuery();
            $_CKEditorIsLoaded = isset($query['_CKEditorIsLoaded']) && $query['_CKEditorIsLoaded'] == 1 ? true : false;

            if (!$_CKEditorIsLoaded) {
                $script = $view->basePath() . '/lib/ckeditor/ckeditor.js';
                $view->headScript()->appendFile($script);
            }
        }
        echo "CKEDITOR.replace('$id', {language: '" . SYSTEM_LANG . "',skin:'moonocolor' });";
        echo "System.Pages.Resources.CKEditor = 1;";
    }

    public static function isAdminRoute()
    {
        $route = getSM()->get('Request')->getRequestUri();
        return (strstr($route, 'admin'));
    }

    public static function RenderTemplate($template, $content)
    {
        $template = getSM('template_table')->get($template)->format;
        return self::RenderTemplateString($template, $content);
    }

    public static function RenderTemplateString($template, $content)
    {
        if (!is_array($content)) {
            $content = array('__CONTENT__' => $content);
        }

        /* @var $dateFormat callable */
        $dateFormat = getSM('ViewHelperManager')->get('dateFormat');

        if (!isset($content['__LONG_DATE__']))
            $content['__LONG_DATE__'] = $dateFormat(time(), 0);

        if (!isset($content['__SHORT_DATE__']))
            $content['__SHORT_DATE__'] = $dateFormat(time(), 4);

        if (!isset($content['__TIME__']))
            $content['__TIME__'] = $dateFormat(time(), -1, 1);

        if (!isset($content['__SITEURL__']))
            $content['__SITEURL__'] = self::siteUrl();

        if (!isset($content['__SITENAME__']))
            $content['__SITENAME__'] = DOMAIN;

        $search = array_keys($content);
        $content = array_values($content);

        return str_replace($search, $content, $template);
    }

    public static function matchDomain($patterns, $visibility)
    {
        $patterns = trim($patterns);
        if (strlen($patterns) > 0) {
            $domain = App::siteUrl();
            if (!isset(self::$_domainRegexps[$patterns])) {
                // Convert path settings to a regular expression.
                // Therefore replace newlines with a logical or, /* with asterisks
                $to_replace = array(
                    '/(\r\n?|\n)/', // newlines
                    '/\\\\\*/', // asterisks
                );
                $replacements = array(
                    '|',
                    '.*',
                );
                $patterns_quoted = preg_quote($patterns, '/');
                self::$_domainRegexps[$patterns] = '/(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')/';
            }

            $match = (bool)preg_match(self::$_domainRegexps[$patterns], $domain);
        } else
            $match = false;

        return //this block should only be rendered in matched domains and we have a match
            (($match == true && $visibility == self::JUST_MATCHED) ||
                //this block should NOT be rendered in matched domains and we don't have a match
                ($match == false && $visibility == self::ALL_BUT_MATCHED));
    }

    public static function matchPath($patterns, $visibility)
    {
        if (!self::$_matchedRoute) {
            /* @var $app \Zend\Mvc\Application */
            $app = getSM('Application');
            self::$_matchedRoute = $app->getRequest()->getRequestUri();
        }

        $path = '/';
        if (self::$_matchedRoute)
            $path = self::$_matchedRoute;
//            $path = url(self::$_matchedRoute->getMatchedRouteName(), self::$_matchedRoute->getParams());

        if (!isset(self::$_regexps[$patterns])) { //TODO add frontpage
            // Convert path settings to a regular expression.
            // Therefore replace newlines with a logical or, /* with asterisks and the <front> with the frontpage.
            $to_replace = array(
                '/(\r\n?|\n)/', // newlines
                '/\\\\\*/', // asterisks
            );
            $replacements = array(
                '|',
                '.*',
            );
            $patterns_quoted = preg_quote($patterns, '/');
            self::$_regexps[$patterns] = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';
        }

        $match = (bool)preg_match(self::$_regexps[$patterns], $path);

        return ( //this block should only be rendered in matched pages and we have a match
            ($match === true && $visibility == self::JUST_MATCHED) ||
            //this block should NOT be rendered in matched pages and we don't have a match
            (!$match === true && $visibility == self::ALL_BUT_MATCHED));
    }

    public static function clearAllCache($site = null)
    {
        if ($site) {
            Directory::clear(ROOT . '/data/' . $site . '/cache');
            /* @var $cache Memcached */
            $cache = getCache(true);
            if ($cache instanceof Memcached)
                $cache->getOptions()->getResourceManager()->getResource(ACTIVE_SITE)->flush();

        } else {
            $cacheDirs = Directory::getDirs(ROOT . '/data', true);
            foreach ($cacheDirs as $cd) {
                Directory::clear($cd . '/cache');
            }
        }
    }

    public static function clearAllPublicCache($cssCache = false, $jsCache = false, $thumbs = false, $captcha = false)
    {
        if ($cssCache) {
            Directory::clear(PUBLIC_FILE . '/cache/css');
        }

        if ($jsCache) {
            Directory::clear(PUBLIC_FILE . '/cache/js');
        }

        if ($thumbs)
            Directory::clear(PUBLIC_FILE . '/thumbs');

        if ($captcha)
            Directory::clear(PUBLIC_FILE . '/captcha');
    }

    public static function hasIntro()
    {
        if (is_null(self::$_hasIntro)) {
            $config = getConfig('system_config')->varValue;

            //main config has intro
            if (isset($config['intro']) && $config['intro'] == '1')
                self::$_hasIntro = true;

            //this domain has intro
            if (isset($config['domains']) && is_array($config['domains'])) {
                if (isset($config['domains'][DOMAIN]) && is_array($config['domains'][DOMAIN])) {
                    if (isset($config['domains'][DOMAIN]['intro']) && $config['domains'][DOMAIN]['intro'] == '1') {
                        self::$_hasIntro = true;
                    } else
                        self::$_hasIntro = false;
                }
            }
        }
        return self::$_hasIntro;
    }

    public static function getUsedCacheSize()
    {
        if (!$cacheSize = getCacheItem('total_used_cache_size')) {
            $path = ROOT . '/data/' . ACTIVE_SITE . '/cache';
            $cacheSize = File::FormatFileSize(Directory::getSize($path));
            setCacheItem('total_used_cache_size', $cacheSize);
        }
        return $cacheSize;
    }
}