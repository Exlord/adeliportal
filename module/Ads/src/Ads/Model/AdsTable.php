<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Ads\Model;

use Application\API\App;
use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway;

class AdsTable extends BaseTableGateway
{
    protected $table = 'tbl_ads';
    protected $model = 'Ads\Model\Ads';
    protected $caches = null;
    protected $cache_prefix = array('ads_urls_', 'ads_search_block_');
    private $ads = null;

    public function getAllSort($where, $itemCount = 20, $page = null, $homePage = 0, $keywordSearch = null, $categorySearch = null, $baseType = null, $fields_table = null, $order = array())
    {
        $baseResult = array(
            'dataArray' => array(),
            'data' => array(),
            'keyword' => array(),
            'category' => array(),
        );

        $protoType = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $sql = $this->getSql();
        $select = $this->getSql()->select();
        $select->columns(array('adId' => 'id', '*'));
        $select->join(array('ao' => 'tbl_ads_order'), $this->table . '.secondType=ao.secondType', array('order','homePage'));
        $order[] = $this->table . '.starCount DESC';
        $order[] = $this->table . '.createDate DESC';
        $order[] = 'ao.order DESC';
        $where->equalTo('ao.homePage', $homePage);
        if ($keywordSearch) {
            $select->join(array('cie' => 'tbl_category_item_entity'), $this->table . '.id=cie.entityId', array('itemId' => 'itemId', 'entityId' => 'entityId'));
            $select->join(array('ci' => 'tbl_category_item'), 'cie.itemId=ci.id', array('itemName'));
            $where->equalTo('cie.entityType', 'ads_keyword_' . $baseType);
            $where->like('ci.itemName', "%" . $keywordSearch . "%");
            $select->group('cie.entityId');
        }
        if ($categorySearch) {
            $select->join(array('cie' => 'tbl_category_item_entity'), $this->table . '.id=cie.entityId', array('itemId' => 'itemId', 'entityId' => 'entityId'));
            $select->join(array('ci' => 'tbl_category_item'), 'cie.itemId=ci.id', array('itemName'));
            $where->equalTo('cie.entityType', 'ads_category_' . $baseType);
            $where->equalTo('ci.itemName', $categorySearch);
            $select->group('cie.entityId');
        }
        if ($fields_table)
            $select->join(array('f' => $fields_table), $this->table . '.id=f.entityId', array('*'), \Zend\Db\Sql\Select::JOIN_LEFT);
        $select->where($where);
        $select->order($order);
         /*print $select->getSqlString($this->getAdapter()->getPlatform());
         die;*/
        $result = $this->getPaginated($select, $sql, $page, $itemCount);
        $categoriesArray = array();
        $keywordsArray = array();
        $data = array();
        if ($result) {
            $entityIdArray = array();

            foreach ($result as $row) {
                $data[] = $row;
                $entityIdArray[] = $row->adId;
            }

            if (!empty($entityIdArray)) {
                $selectKeywords = getSM('entity_relation_table')->getItems($entityIdArray, 'ads_keyword_' . $baseType);
                if ($selectKeywords)
                    foreach ($selectKeywords as $row)
                        $keywordsArray[$row->entityId][$row->itemId] = $row->itemName;

                $selectCategories = getSM('entity_relation_table')->getItems($entityIdArray, 'ads_category_' . $baseType);
                if ($selectCategories)
                    foreach ($selectCategories as $row)
                        $categoriesArray[$row->entityId][$row->itemId] = $row->itemName;
            }
            $baseResult['dataArray'] = $data;
            $baseResult['data'] = $result;
            $baseResult['keyword'] = $keywordsArray;
            $baseResult['category'] = $categoriesArray;
        }
        $this->resultSetPrototype = $protoType;

        return $baseResult;

    }

    public function getAd($baseType, $id, $fields_table)
    {

        $baseResult = array(
            'dataArray' => array(),
            'keyword' => array(),
            'category' => array(),
        );
        $protoType = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $select = $this->getSql()->select();
        $select->columns(array('adId' => 'id', '*'));
        //$file_join = new Db\Sql\Expression($this->table . '.id=i.entityId AND i.entityType="ads_' . $baseType . '"');
        if ($fields_table)
            $select->join(array('tf' => $fields_table), $this->table . '.id=tf.entityId', array('*'), \Zend\Db\Sql\Select::JOIN_LEFT);
        $select
            ->join(array('cl' => 'tbl_city_list'), $this->table . '.cityId=cl.id', array('cityTitle'), Select::JOIN_LEFT)
            ->join(array('s' => 'tbl_state_list'), $this->table . '.stateId=s.id', array('stateTitle'), Select::JOIN_LEFT);
        // ->join(array('i' => 'tbl_file'), $file_join, array('fPath' => new Db\Sql\Expression('GROUP_CONCAT(i.fPath)'), 'fAlt' => new Db\Sql\Expression('GROUP_CONCAT(i.fAlt)'), 'fTitle' => new Db\Sql\Expression('GROUP_CONCAT(i.fTitle)')), Select::JOIN_LEFT);
        $select->where(array( /*$this->table . '.status' => 1,*/
            $this->table . '.id' => $id));
        /*print $select->getSqlString($this->getAdapter()->getPlatform());
        die;*/
        $result = $this->selectWith($select)->current();
        $this->resultSetPrototype = $protoType;


        // $result = $this->get($id);
        $categoriesArray = array();
        $keywordsArray = array();
        $data = array();
        if ($result) {
            $entityIdArray = array();
            $baseResult['dataArray'] = $result;
            $entityIdArray[] = $result->entityId;
            if (!empty($entityIdArray)) {
                $selectKeywords = getSM('entity_relation_table')->getItems($entityIdArray, 'ads_keyword_' . $baseType);
                if ($selectKeywords)
                    foreach ($selectKeywords as $row)
                        $keywordsArray[$row->entityId][$row->itemId] = $row->itemName;

                $selectCategories = getSM('entity_relation_table')->getItems($entityIdArray, 'ads_category_' . $baseType);
                if ($selectCategories)
                    foreach ($selectCategories as $row)
                        $categoriesArray[$row->entityId][$row->itemId] = $row->itemName;
            }
            $baseResult['keyword'] = $keywordsArray;
            $baseResult['category'] = $categoriesArray;
        }
        return $baseResult;
    }

    public function getAdsArrayForList($baseType, $secondType, $starCount = 0, $count = 0)
    {

        $dataArray = array();
        if ($baseType && $secondType) {

            $fields_table = getSM()->get('fields_api')->init('ads_' . $baseType);
            $protoType = $this->resultSetPrototype;
            $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
            $select = $this->getSql()->select();
            $select->columns(array('adId' => 'id', '*'));
            $select
                ->join(array('cl' => 'tbl_city_list'), $this->table . '.cityId=cl.id', array('cityTitle'), Select::JOIN_LEFT)
                ->join(array('s' => 'tbl_state_list'), $this->table . '.stateId=s.id', array('stateTitle'), Select::JOIN_LEFT);
            // $file_join = new Db\Sql\Expression($this->table . '.id=i.entityId AND i.entityType="ads_' . $baseType . '"');
            if ($fields_table)
                $select->join(array('tf' => $fields_table), $this->table . '.id=tf.entityId', array('*'), \Zend\Db\Sql\Select::JOIN_LEFT);
            // $select
            //     ->join(array('i' => 'tbl_file'), $file_join, array('fPath' => new Db\Sql\Expression('GROUP_CONCAT(i.fPath)'), 'fAlt' => new Db\Sql\Expression('GROUP_CONCAT(i.fAlt)'), 'fTitle' => new Db\Sql\Expression('GROUP_CONCAT(i.fTitle)')), Select::JOIN_LEFT);
            $select->where(array(
                $this->table . '.status' => 1,
                $this->table . '.baseType' => $baseType,
                $this->table . '.secondType' => $secondType,
                $this->table . '.starCount' => $starCount,
                $this->table . '.expireDate > ?' => time()));
            $select->order($this->table . '.secondType ASC , starCount DESC');
            if ($count)
                $select->limit((int)$count);
            /*print $select->getSqlString($this->getAdapter()->getPlatform());
            die;*/
            $result = $this->selectWith($select);
            $this->resultSetPrototype = $protoType;
            if ($result)
                foreach ($result as $row)
                    $dataArray[$row->adId] = $row;
        }
        return $dataArray;
    }

    public function createLinkItems($parents, $pId)
    {
        $items = array();
        $this->__sortItemForSiteMap($parents, $pId, $items);
        return $items;
    }

    private function __sortItemForSiteMap(&$parents, $pId, &$items)
    {
        if (isset($parents[$pId])) {
            foreach ($parents[$pId] as $item) {
                $items[$item->id]['data'] = Common::Link($item->itemName, url('app/content', array('tagId' => $item->id, 'tagName' => $item->itemName)));
                $this->__sortItemForSiteMap($parents, $item->id, $items[$item->id]['children']);
            }
        }
    }

    public function getUrl($ad, $baseType, $urlTitle = '', $mvc = false)
    {
        $cache_key = 'ads_urls_' . $baseType;
        if (!is_object($ad)) {
            $adId = $ad;

            if (is_null($this->ads)) {
                if (!$this->ads = getCacheItem($cache_key))
                    $this->ads = array();
            } else
                if (isset($this->ads[$adId]))
                    return $this->ads[$adId];
        } else
            $adId = $ad->id;


        if ($ad instanceof \Ads\Model\Ads)
            $adObject = $ad;
        else
            $adObject = $this->get($adId);
        if ($adObject) {
            $url = url('app/ad/view', array('baseType' => $baseType, 'adId' => $adObject->id, 'adTitle' => App::prepareUrlString($urlTitle)));
        } else
            $url = '#';

        $this->ads[$adId] = $url;
        if (!$ad instanceof \Ads\Model\Ads)
            setCacheItem($cache_key, $this->ads);

        return $url;
    }

    public function getAdsForBlock($t_array, $f_array, $baseType, $isRequest)
    {
        $list = array();
        $this->swapResultSetPrototype();
        if (is_array($t_array)) {
            foreach ($t_array as $key => $val) {
                $keyName = '';
                $columnsArray = array();
                $select = $this->getSql()->select();
                $where = array(
                    'status' => array(1, 2),
                    'baseType' => $baseType,
                );
                switch ($key) {
                    case 'countryId':
                        $dataArray = getSM('state_table')->getArray($val);
                        if ($dataArray) {
                            $where['stateId'] = array_keys($dataArray);
                            $select->join(array('sl' => 'tbl_state_list'), $this->table . '.stateId=sl.id', array('id' => 'id'), \Zend\Db\Sql\Select::JOIN_LEFT);
                            $columnsArray[] = new Db\Sql\Expression('COUNT(stateId) as count');
                            $select->group($this->table . '.stateId');
                            $keyName = 'stateId';
                        }
                        break;
                    case 'stateId':
                        $dataArray = getSM('city_table')->getArray($val);
                        if ($dataArray) {
                            $where['cityId'] = array_keys($dataArray);
                            $select->join(array('cl' => 'tbl_city_list'), $this->table . '.cityId=cl.id', array('id' => 'id'), \Zend\Db\Sql\Select::JOIN_LEFT);
                            $columnsArray[] = new Db\Sql\Expression('COUNT(cityId) as count');
                            $select->group($this->table . '.cityId');
                            $keyName = 'cityId';
                        }
                        break;
                }
                $select->columns($columnsArray);
                $select->where($where);
                /* print $select->getSqlString($this->getAdapter()->getPlatform());
                 die;*/
                $select = $this->selectWith($select);
                if ($select)
                    foreach ($select as $row)
                        $list['table'][$keyName][] = array(
                            'name' => $dataArray[$row['id']],
                            'count' => $row['count'],
                            'id' => $row['id'],
                        );
            }
        }
        if (is_array($f_array)) {
            $entityType = 'ads_' . $baseType . '_' . $isRequest;
            $fields_table = getSM()->get('fields_api')->init($entityType);
            foreach ($f_array as $key => $row) {
                if (is_array($row)) {
                    foreach ($row as $item)
                        $fieldArray[$item['key']] = $item['value'];
                }

                $select = $this->getSql()->select();
                $where = array(
                    'status' => array(1, 2),
                );
                $columnsArray = array();
                $select->join(array('f' => $fields_table), $this->table . '.id=f.entityId', array($key), \Zend\Db\Sql\Select::JOIN_LEFT);
                $columnsArray[] = new Db\Sql\Expression('COUNT(' . $this->qi($key) . ') as count');
                $select->group('f.' . $key);
                $select->columns($columnsArray);
                $select->where($where);
                $select = $this->selectWith($select);
                if ($select)
                    foreach ($select as $rows)
                        $list['fields'][$key][] = array(
                            'name' => $f_array[$key][$rows[$key]]['value'],
                            'count' => $rows['count'],
                            'id' => $rows[$key],
                        );
            }
        }
        $this->swapResultSetPrototype();
        return $list;
    }

    public function  getExpired($type = 0)
    {
        if ($type == 1) {
            $threeDay = strtotime('- 3 days');
            $twoDay = strtotime('- 2 days');
            return $this->getAll(array('expireDate > ?' => $threeDay, 'expireDate < ?' => $twoDay, 'status' => 1));
        } else
            return $this->getAll(array('status' => 1, 'expireDate < ?' => time()));
    }

    public function getRegType($adId)
    {
        $regType = 0;
        if ($adId) {
            $select = $this->get($adId);
            if (isset($select->regType) && $select->regType)
                $regType = $select->regType;
        }
        return $regType;
    }

    public function adCountByCatId($catId)
    {

    }

    public function getFieldsRangeValue($table, $fields, $baseType)
    {
        if (!count($fields))
            return array();
        $cacheKey = 'ads_search_block_' . $baseType;
        if ($list = getCache()->getItem($cacheKey)) {
            $allFields = true;
            if (isset($list['field'])) {
                foreach ($fields as $f) {
                    if (!isset($list['field']['max_' . $f])) {
                        $allFields = false;
                        break;
                    }
                }
                if ($allFields)
                    return $list['field'];
            }
        }

        $sql = new Db\Sql\Sql(App::getDbAdapter());
        $select = $sql->select($table);

        $columns = array();
        foreach ($fields as $f) {
            $columns[] = new Db\Sql\Expression('MAX(' . $f . ') as max_' . $f);
            $columns[] = new Db\Sql\Expression('MIN(' . $f . ') as min_' . $f);
        }
        $select->columns($columns);
        $statement = $sql->prepareStatementForSqlObject($select);
        $list['field'] = $statement->execute()->current();

        getCache()->setItem($cacheKey, $list);
        return $list['field'];
    }

    public function getAdsList(Db\Sql\Select $select, $baseType)
    {
        $select->where->equalTo($this->table . '.baseType', $baseType);
        if (!isAllowed(\Ads\Module::ADMIN_AD_LIST_ALL)) {
            $select->where->equalTo($this->table . '.userId', current_user()->id);
            $currentRole = current_user()->roles;
            if ($currentRole)
                foreach ($currentRole as $row)
                    $select->where->or->equalTo('r.roleId', $row['id']);
            $select->where->or->equalTo('r.userId', current_user()->id);
        }
        $select->join(array('r' => 'tbl_ads_ref'), $this->table . '.id=r.adId', array('adId', 'roleId', 'senderId', 'userRefId' => 'userId'), 'left');
        $select->join(array('u' => 'tbl_users'), 'r.senderId=u.id', array('displayName'), 'left');
        $select
            ->join(array('cl' => 'tbl_city_list'), $this->table . '.cityId=cl.id', array('cityTitle'), Select::JOIN_LEFT)
            ->join(array('s' => 'tbl_state_list'), $this->table . '.stateId=s.id', array('stateTitle'), Select::JOIN_LEFT);
        $select->group($this->table . '.id');
        $select->order($this->table.'.id DESC');
        /* print $select->getSqlString($this->getAdapter()->getPlatform());
         die;*/
        /*if (getSM()->has('domain_api')) {
            $domainApi = getSM('domain_api');
            $domainCount = getSM('domain_table')->getCount();
            if ($domainCount > 1)
                $domainApi->filter($select, 'PAGE', $this->table . '.id');
        }*/
    }

    public function getLikeRequest($baseType, $where, $fields_table = null)
    {
        $protoType = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        // $sql = $this->getSql();
        $select = $this->getSql()->select();
        $select->columns(array('adId' => 'id', '*'));
        /*if ($keywordSearch) {
            $select->join(array('cie' => 'tbl_category_item_entity'), $this->table . '.id=cie.entityId', array('itemId' => 'itemId', 'entityId' => 'entityId'));
            $select->join(array('ci' => 'tbl_category_item'), 'cie.itemId=ci.id', array('itemName'));
            $where->equalTo('cie.entityType', 'ads_keyword_' . $baseType);
            $where->like('ci.itemName', "%" . $keywordSearch . "%");
            $select->group('cie.entityId');
        }
        if ($categorySearch) {
            $select->join(array('cie' => 'tbl_category_item_entity'), $this->table . '.id=cie.entityId', array('itemId' => 'itemId', 'entityId' => 'entityId'));
            $select->join(array('ci' => 'tbl_category_item'), 'cie.itemId=ci.id', array('itemName'));
            $where->equalTo('cie.entityType', 'ads_category_' . $baseType);
            $where->equalTo('ci.itemName', $categorySearch);
            $select->group('cie.entityId');
        }*/
        if ($fields_table)
            $select->join(array('f' => $fields_table), $this->table . '.id=f.entityId', array('*'), \Zend\Db\Sql\Select::JOIN_LEFT);
        $select->where($where);
        //->order($order);
        /*print $select->getSqlString($this->getAdapter()->getPlatform());
        die;*/
        $result = $this->selectWith($select);
        return $result;
    }
}
