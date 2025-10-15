<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 3/1/14
 * Time: 4:10 PM
 */

namespace Application\API;


use System\API\BaseAPI;

class Search extends BaseAPI
{
    const SYSTEM_WIDE_SEARCH = 'system_wide_search';

    public function systemSearch($keyword)
    {
        $search = new \stdClass();
        $search->keyword = $keyword;
        $search->data = array();
        $this->getEventManager()->trigger(self::SYSTEM_WIDE_SEARCH, $this, array('search' => &$search));
        return $search->data;
    }
} 