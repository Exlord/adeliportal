<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Domain\API;

use System\API\BaseAPI;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Form\Element;

class Domain extends BaseAPI
{
    /**
     * @param $identityField
     * @param $mainSelect Select
     * @param $entityType
     */
//    public function filterByDomain($identityField, $mainSelect, $entityType)
//    {
//        $select = new Select();
//        $select
//            ->from(array('dc' => 'tbl_domain_content'))
//            ->columns(array('entityId'))
//            ->join(array('d' => 'tbl_domains'), 'd.id=dc.domainId', array())
//            ->where(array(
//                'd.domain' => DOMAIN,
//                'dc.entityType' => $entityType
//            ));
//
//        $mainSelect->where->addPredicate(new In($identityField, $select));
//    }

    /**
     * @param $mainSelect Select
     * @param $entityType
     * @param $identityField
     */
    public function filter($mainSelect, $entityType, $identityField)
    {
        $select = new Select();
        $select
            ->from('tbl_domain_content')
            ->columns(array(new Expression('DISTINCT(entityId) as entityId')))
            ->join('tbl_domains', 'tbl_domains.id=tbl_domain_content.domainId', array())
            ->where(array(
                'tbl_domains.domain' => DOMAIN,
                'tbl_domain_content.entityType' => $entityType
            ));

        $select2 = new Select();
        $select2
            ->from('tbl_domain_content')
            ->columns(array(new Expression('DISTINCT(entityId) as entityId')))
            ->where(array(
                'tbl_domain_content.entityType' => $entityType
            ));

        $where = new Where();

        $where->addPredicate(new In($identityField, $select));
        $where->addPredicate(new NotIn($identityField, $select2), 'OR');

        $mainSelect->where->addPredicate($where);
    }
}