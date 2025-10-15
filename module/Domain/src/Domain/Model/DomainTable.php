<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Domain\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway;

class DomainTable extends BaseTableGateway
{
    protected $table = 'tbl_domains';
    protected $model = 'Domain\Model\Domain';
    protected $caches = array("domains_list");
    protected $cache_prefix = null;

    public function getArray()
    {
        $cacheKey = "domains_list";
        if ($domainsArray = getCacheItem($cacheKey))
            return $domainsArray;

        $domains = $this->getAll();
        $domainsArray = array();
        if ($domains) {
            foreach ($domains as $d) {
                $domainsArray[$d->id] = $d->domain;
            }
        }
        setCacheItem($cacheKey, $domainsArray);
        return $domainsArray;
    }

    public function getCount()
    {
        $cacheKey = "domains_count";
        if ($domainsCount = getCache(true)->getItem($cacheKey))
            return $domainsCount;

        $domainsCount = count($this->getArray());
        getCache(true)->setItem($cacheKey, $domainsCount);

        return $domainsCount;
    }

    public function remove($id)
    {
        getSM('domain_content_table')->remove($id);
        parent::remove($id);
    }
}
