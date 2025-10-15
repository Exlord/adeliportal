<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 4/6/14
 * Time: 11:17 AM
 */

namespace Menu\Navigation\Page;


use Application\API\App;

class Mvc extends \Zend\Navigation\Page\Mvc
{
    private $_aliasCache = null;

    public function getHref()
    {
        if ($this->_aliasCache)
            return $this->_aliasCache;

        $url = parent::getHref();

        if ($this->route == 'app/front-page' && !App::hasIntro()) {
            $langTable = getSM('language_table');
            $lang = $langTable->getDefaultLang();
            if ($url == '/' . $lang . '/front-page')
                $url = '/';
        }

        $aliasUrlTable = getSM('alias_url_table');
        if ($aliasUrlTable) {
            $alias = $aliasUrlTable->getByUrl(urldecode($url));
            if ($alias)
                $this->_aliasCache = $alias;
        }
        if (!$this->_aliasCache)
            $this->_aliasCache = $url;

        return $this->_aliasCache;
    }
} 