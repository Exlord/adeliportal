<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 4/6/14
 * Time: 12:12 PM
 */

namespace Menu\Navigation\Page;


class Uri extends \Zend\Navigation\Page\Uri{
    private $_aliasCache = null;

    public function getHref()
    {
        if ($this->_aliasCache)
            return $this->_aliasCache;

        $url = parent::getHref();

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