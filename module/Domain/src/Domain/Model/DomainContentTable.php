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

class DomainContentTable extends BaseTableGateway
{
    protected $table = 'tbl_domain_content';
//    protected $model = 'Domain\Model\Domain';
    protected $caches = null;
    protected $cache_prefix = null;

    public function remove($domainId)
    {
        $this->delete(array('domainId' => $domainId));
    }

    public function removeByEntity($entityId, $entityType)
    {
        $this->delete(array('entityId' => $entityId, 'entityType' => $entityType));
    }

    public function add(array $domains, $entityId, $entityType)
    {
        if (count($domains)) {
            foreach ($domains as $d) {
                if (!empty($d)) {
                    $this->insert(array(
                        'domainId' => $d,
                        'entityId' => $entityId,
                        'entityType' => $entityType
                    ));
                }
            }
        }
    }

    public function getDomains($entityId, $entityType)
    {
        $select = $this->getSql()->select();
        $select
            ->columns(array('domainId'))
            ->where(array('entityId' => $entityId, 'entityType' => $entityType));
        $domains = $this->selectWith($select);
        $domainsArray = array();
        foreach ($domains as $d) {
            $domainsArray[] = $d->domainId;
        }
        return $domainsArray;
    }
}
