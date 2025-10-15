<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace RealEstate\Model;

use Application\API\App;
use System\DB\BaseTableGateway;
use Theme\API\Common;
use Zend\Db;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway;
use Zend\Paginator\Paginator;

class RealEstateTable extends BaseTableGateway
{
    const SEARCH_CACHE_KEY = 'realestate_search_form';
    protected $table = 'tbl_realestate';
    protected $model = 'RealEstate\Model\RealEstate';
    protected $caches = array('real_estate_widget', 'real_estate_urls', self::SEARCH_CACHE_KEY);
    protected $cache_prefix = array('statistics_real_estate_', 'region_statistic_', 'all_realestate_counts');
    protected $realEstateUrl = array();

    public static $RealStatesStatus = array(
        'all' => -1,
//        'not-approved' => 0,
        'not_approved' => 0,
        'approved' => 1,
        'archived' => 2,
        'transferred' => 3,
        'canceled' => 4,
        'deleted' => 5
    );

    public static $RealStatesStatusView = array(
        -1 => 'All',
        0 => 'Not Approved',
        1 => 'Approved',
        2 => 'Archived',
        3 => 'Transferred',
        4 => 'Canceled',
        5 => 'Deleted'
    );

    /**
     * @var array
     */
    public $expireTime = array(
        //0 => 'بدون تغییر',
        1 => '1 Month',
        3 => '3 Month',
        6 => '6 Month',
        12 => '12 Month',
    );

    public static $estateRegType = array(
        1 => 'Sell',
        2 => 'Mortgage and Rent',
        3 => 'Pre-Sale',
        4 => 'Construction Partnership'
    );

    public static $estateRegTypeRequest = array(
        1 => 'REALESTATE_BUY',
        2 => 'Mortgage and Rent',
        3 => 'Pre-Sale',
        4 => 'Construction Partnership'
    );

    public static $isRequest = array(
        0 => 'Registered RealEstates',
        1 => 'Requested Estates',
    );

    public static $prices;

    /*$cache_key = $this->getServiceLocator()->get('fields_api')->getCacheKey('real_estate');
    $cache_item = getCacheItem($cache_key);
    $fields_table = false;
    if ($cache_item)
    $fields_table = $cache_item['table'];*/

    public function getAll($fields_table, $page, $where = null, $order = null, $keyword = null, $itemCount = 20, $hasArea = 0)
    {

        $this->swapResultSetPrototype(new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS));
        $sql = $this->getSql();
        $select = $this->getSelect($sql, $fields_table, $hasArea);
        if ($where)
            $select->where($where);
        iF ($order)
            $select->order($order);

        /*  if ($orWhere)
              $select->where($orWhere,\Zend\Db\Sql\Predicate\Predicate::OP_OR);*/

        if ($keyword) {
            $orWhere = '(';
            $keyword = $this->getAdapter()->getPlatform()->quoteValue('%' . $keyword . '%');
            $orWhere .= $this->table . '.id like ' . $keyword . ' OR ';
            $orWhere .= $this->table . '.ownerName like ' . $keyword . ' OR ';
            $orWhere .= $this->table . '.ownerMobile like ' . $keyword . ' OR ';
            $orWhere .= $this->table . '.ownerPhone like ' . $keyword . ' OR ';
            $orWhere .= $this->table . '.ownerEmail like ' . $keyword . ' OR ';
            $orWhere .= $this->table . '.addressShort like ' . $keyword . ' OR ';
            $orWhere .= $this->table . '.addressFull like ' . $keyword . ' OR ';
            $orWhere .= $this->table . '.description like ' . $keyword . ')';
            $select->where($orWhere);
        }

          /*print($select->getSqlString($this->getAdapter()->getPlatform()));
          die;*/
        if ($page != null)
            $result = $this->getPaginated($select, $sql, $page, $itemCount);
        else
            $result = $this->selectWith($select);

        $this->swapResultSetPrototype();
        return $result;
    }

    public function getPaginated($select, $sql, $page, $itemCount = 20)
    {
//        $adapter = new DbSelect($select, $sql);
        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $pagination = new Paginator($adapter);
        $pagination->setCurrentPageNumber($page);
        $pagination->setItemCountPerPage($itemCount);
        return $pagination;
    }

    public static function makePrices()
    {
        $cache_key = 'real_estate_prices';
        if (!self::$prices = getCacheItem($cache_key)) {
            $amount = 'میلیون';
            $amount2 = 'میلیارد';
            $unit = ' ' . 'تومان';
            $step = 10;
            $between = t('Between');
            $and = ' ' . t('and') . ' ';
            $divider = 1;
            for ($i = 0; $i <= 10000; $i += $step) {

                if ($i == 100)
                    $step = 50;
                elseif ($i == 500) {
                    $step = 100;
                } elseif ($i == 1000) {
                    $step = 1000;
                    $divider = 1000;
                    $amount = $amount2;
                }

                $priceTo = ($i + $step);
                if ($i == 0)
                    $text = t('Under') . ' 10 ' . $amount . $unit;
                elseif ($i == 900) {
                    $text = $between . ' 900 ' . $amount . $and . '1 ' . $amount2 . $unit;
                } elseif ($i == 10000) {
                    $text = t('Over') . ' 10 ' . $amount . $unit;
                    $priceTo = 0;
                } else
                    $text = $between . ' ' . $i / $divider . $and . ($i + $step) / $divider . ' ' . $amount . $unit;

                self::$prices[$i . '-' . $priceTo] = $text;
            }
            setCacheItem($cache_key, self::$prices);
        }
        return self::$prices;
    }

    /**
     * @param $sql \Zend\Db\Sql\Sql
     */
    private function getSelect($sql, $fields_table, $hasArea = 0)
    {
        $columns = $this->getFields();

        if (($key = array_search('id', $columns)) !== false) {
            unset($columns[$key]);
        }
        $columns['itemId'] = 'id';

        $file_join = new Db\Sql\Expression($this->table . '.id=i.entityId AND i.entityType="real_estate"');
        $select = $sql
            ->select()
            ->columns($columns)
            ->group($this->table . '.id');
        if ($hasArea) {
            $joinSide = $hasArea == 1 ? 'inner' : 'left';
            $select
                ->join(array('ca' => 'tbl_city_area_list'), $this->table . '.areaId=ca.id', array('areaTitle'), Select::JOIN_LEFT)
                ->join(array('ca2' => 'tbl_city_area_list'), 'ca.parentId=ca2.id', array('areaTitleParent' => 'areaTitle'), $joinSide);
        }
        $select
            ->join(array('cl' => 'tbl_city_list'), $this->table . '.cityId=cl.id', array('cityTitle'), Select::JOIN_LEFT)
            ->join(array('s' => 'tbl_state_list'), $this->table . '.stateId=s.id', array('stateTitle'), Select::JOIN_LEFT)
            ->join(array('u' => 'tbl_users'), $this->table . '.userId=u.id', array('username', 'email', 'displayName',), Select::JOIN_LEFT)
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'phone', 'mobile'), Select::JOIN_LEFT)
            ->join(array('ur' => 'tbl_users_roles'), 'ur.userId=u.id', array('roleId' => new Db\Sql\Expression('GROUP_CONCAT(ur.roleId)')), Select::JOIN_LEFT)
            ->join(array('ci' => 'tbl_category_item'), $this->table . '.estateType=ci.id', array('estateTypeName' => 'itemName'), Select::JOIN_LEFT)
            ->join(array('f' => $fields_table), $this->table . '.id=f.entityId', array('*'), Select::JOIN_LEFT)
            ->join(array('i' => 'tbl_file'), $file_join, array('fPath' => new Db\Sql\Expression('GROUP_CONCAT(i.fPath)')), Select::JOIN_LEFT)//            ->join(array('i' => 'tbl_file'), $this->table . '.id=i.entityId', array('fPath' => new Db\Sql\Expression('GROUP_CONCAT(i.fPath)')), \Zend\Db\Sql\Select::JOIN_LEFT)
        ;
        return $select;
    }

    public function get($id, $fields_table, $where = array(), $hasArea = 1)
    {
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $sql = $this->getSql();
        $select = $this->getSelect($sql, $fields_table, $hasArea);
        $select->where(array($this->table . '.id' => $id));
        if ($where)
            $select->where($where);
        $result = $this->selectWith($select)->current();
        return $result;
    }

    public function checkForEdit($id, $code, $mobile)
    {
        $result = $this->select(array(
            'id' => $id,
            'ownerMobile' => $mobile,
            'passForEdit' => $code
        ))->current();
        return $result;

    }

    public function getForEdit($id, $isRequest = False)
    {
        $realState = $this->toArray(parent::get($id));
        $fields_id_list = '';
        if (!$isRequest) {
            $file = getSM()->get('file_table')->getByEntityType('real_estate', $id);
            $fileArray = array();
            foreach ($file as $value)
                $fileArray[] = $value->fPath;
            $filed_api = getSM()->get('fields_api');

            $fields_table = $filed_api->init('real_estate');
            $fieldData = $filed_api->getFieldData($id);

            $realState['images'] = $fileArray;
            $realState['transferFields'] = $fieldData;
        }

        return $realState;
    }

    public function getForUpdate($id, $columns = false)
    {
        return parent::get($id, $columns);
    }

    public function getCounts()
    {
        $cacheKey = 'all_realestate_counts' . current_user()->id;
        if ($data = getCacheItem($cacheKey))
            return $data;

        $data = array();
        $data['count']['special'] = 0;
        $data['count']['requested'] = 0;
        $data['count']['all'] = 0;
        foreach (self::$RealStatesStatus as $type => $key) {
            $data['count'][$type] = 0;
        }

        $where = array();
        if (!isAllowed(\RealEstate\Module::ADMIN_REAL_ESTATE_VIEW_ALL))
            $where['userId'] = current_user()->id;

        $this->swapResultSetPrototype();
        $select = $this->getSql()->select();
        $select->where($where);
        $select->columns(array('status', 'isSpecial', 'userId', 'isRequest', 'expireSpecial', 'expire'));
        $result = $this->selectWith($select);

        $time = time();
        $types = array_flip(self::$RealStatesStatus);
        foreach ($result as $row) {
            if (isset($types[$row['status']]))
                $data['count'][$types[$row['status']]] += 1;

            if (
                isset($row['isSpecial']) &&
                (int)$row['isSpecial'] == 1 &&
                $row['expireSpecial'] > $time &&
                (int)$row['status'] == 1 &&
                $row['expire'] > $time
            )
                $data['count']['special'] += 1;

            if (
                isset($row['isRequest']) &&
                (int)$row['isRequest'] == 1 &&
                (int)$row['status'] == 1 &&
                $row['expire'] > $time
            )
                $data['count']['requested'] += 1;

            $data['count']['all'] += 1;
        }
        $this->swapResultSetPrototype();

//        $this->swapResultSetPrototype();
//        foreach (self::$RealStatesStatus as $type => $key) {
//            $type = str_replace('-', '_', $type);
//            $select = $this->getSql()->select();
//            $select->columns(array($type => new \Zend\Db\Sql\Expression('COUNT(*)')));
//            if ($key > -1)
//                $where['status'] = $key;
//            else
//                unset($where['status']);
//            $select->where($where);
//            $row = $this->selectWith($select)->current();
//            $data['count'][$type] = $row[$type];
//        }
//        $sql = $this->getSql();
//        $this->swapResultSetPrototype();
        setCacheItem($cacheKey, $data);
        return $data;
    }

    public function getCountByUserId($userId)
    {
        $select = $this->getSql()->select();
        $select->columns(array('id' => new \Zend\Db\Sql\Expression('COUNT(*)')))
            ->where(array(
                    'userId' => $userId,
                )
            );
        $result = $this->selectWith($select)->current();
        $count = $result->id;
        return $count;
    }

    public function  getExpired($type = 0)
    {
        if ($type == 1) {
            $threeDay = strtotime('- 3 days');
            $twoDay = strtotime('- 2 days');
            return $this->getrealestate(array('expire > ?' => $threeDay, 'expire < ?' => $twoDay, 'status' => 1));
        } else
            return $this->getrealestate(array('status' => 1, 'expire < ?' => time()));
    }

    public function  getSpecialExpired($type = 0)
    {
        if ($type == 1) {
            $threeDay = strtotime('- 3 days');
            $twoDay = strtotime('- 2 days');
            return $this->getrealestate(array('expireSpecial > ?' => $threeDay, 'expireSpecial < ?' => $twoDay, 'isSpecial' => 1));
        } else
            return $this->getrealestate(array('isSpecial' => 1, 'expireSpecial < ?' => time()));
    }

    public function  getShowInfoExpired($type = 0)
    {
        if ($type == 1) {
            $threeDay = strtotime('- 3 days');
            $twoDay = strtotime('- 2 days');
            return $this->getrealestate(array('expireShowInfo > ?' => $threeDay, 'expireShowInfo < ?' => $twoDay, 'showInfo' => 1));
        } else
            return $this->getrealestate(array('showInfo' => 1, 'expireShowInfo < ?' => time()));
    }

    public function getAllSpecial()
    {
        $where = array(
            'isSpecial' => 1,
            'expireSpecial > ?' => time(),
            'status' => 1,
            'expire > ?' => time(),
            'userId' => current_user()->id
        );
        $sql = $this->getSql();
        $select = $sql->select();
        if ($where)
            $select->where($where);

        $data = $this->selectWith($select)->count();

        return $data;

    }

    public function getrealestate($where = null, $order = null, $limit = null, $pageNumber = false)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        if ($where)
            $select->where($where);
        if ($order)
            $select->order($order);
        if ($limit)
            $select->limit($limit);

        if ($pageNumber) {
            return $this->getPaginated($select, $sql, $pageNumber);
        } else {
            return $this->selectWith($select);
        }
    }

    public function getAllRequestedEstate()
    {
        $where = array(
            'status' => 1,
            'expire > ?' => time(),
            'isRequest' => 1,
            'userId' => current_user()->id
        );
        $sql = $this->getSql();
        $select = $sql->select();
        if ($where)
            $select->where($where);

        $data = $this->selectWith($select)->count();
        return $data;
    }

    public function getUrl($realEstate)
    {
        /*$cache_key = 'real_estate_urls';
        $realEstateId = $realEstate->id;

        if ($realEstate instanceof \RealEstate\Model\RealEstate)
            $estate = $realEstate;
        else
            $estate = $this->getRealestate(array('id' => $realEstateId))->current();*/

        if ($realEstate) {
            $title = $realEstate->estateTypeName . ' ' . t('REALESTATE_IN') . ' ' . $realEstate->cityTitle . ' ' . t('REALESTATE_WITH_CODE') . ' ' . $realEstate->itemId;
            $this->realEstateUrl[$realEstate->itemId] = array(
                'url' => url('app/real-estate/view', array('id' => $realEstate->itemId, 'title' => App::prepareUrlString($title))),
                'title' => $title,
            );
        } else
            $url = '#';

        /*$this->realEstateUrl[$realEstateId] = $url;
        if (!$realEstate instanceof \RealEstate\Model\RealEstate)
            setCacheItem($cache_key, $this->realEstateUrl);*/

        return $this->realEstateUrl[$realEstate->itemId];
    }

    public function getLatestRealEstateArrayForList($estate_type, $estate_reg_type, $count = 5)
    {
        $fields_table = getSM()->get('fields_api')->init('real_estate');
        $dataArray = array();
        if ($estate_type) {
            $select = $this->getAll($fields_table, null, array(
                $this->table . '.estateType' => $estate_type,
                $this->table . '.regType' => $estate_reg_type,
                $this->table . '.status' => 1,
                $this->table . '.expire > ?' => time(),
            ), $this->table . '.modified DESC', null, $count, 0);
            if ($select)
                foreach ($select as $row)
                    $dataArray[$row->regType][$row->id] = $row;

        }
        return $dataArray;
    }

    public function getRealEstateArrayForList($estate_type, $estate_reg_type, $count = 5, $type)
    {
        $dataArray = array();
        if ($estate_type) {
            $fields_table = getSM()->get('fields_api')->init('real_estate');
            $where = array(
                $this->table . '.estateType' => $estate_type,
                $this->table . '.regType' => $estate_reg_type,
                $this->table . '.status' => 1,
                $this->table . '.expire > ?' => time(),
            );
            if ($type == 2) {
                $where[$this->table . '.isSpecial'] = 1;
                $where[$this->table . '.expireSpecial > ?'] = time();
            }
            $select = $this->getAll($fields_table, null, $where, 'modified DESC', null, $count);
            if ($select)
                foreach ($select as $row)
                    $dataArray[$row->id] = $row;

        }
        return $dataArray;
    }

    public function getEstateList(Select $select, $flagShow) //for data grid
    {
        $select
            ->group($this->table . '.id')
            ->join(array('ca' => 'tbl_city_area_list'), $this->table . '.areaId=ca.id', array('areaTitle'), 'left')
            ->join(array('c' => 'tbl_city_list'), $this->table . '.cityId=c.id', array('cityTitle'), 'left')
            ->join(array('s' => 'tbl_state_list'), $this->table . '.stateId=s.id', array('stateTitle'), 'left')
            ->join(array('ci' => 'tbl_category_item'), $this->table . '.estateType=ci.id', array('estateTypeName' => 'itemName'), Select::JOIN_LEFT);
//        $select->order(array( /*$this->table . '.isSpecial DESC',*/
//            $this->table . '.created DESC'));

        if (!$flagShow) {
            $userId = current_user()->id;
            $predicate = new  \Zend\Db\Sql\Where();
            $selectArea = new Select('tbl_realestate_agent_area');
            $selectArea->columns(array('areaId'))
                ->where(array('agentId' => $userId));
            $select->where($predicate->in($this->table . '.areaId', $selectArea));
            $where['tbl_realestate.userId'] = $userId;
            $select->where($where, 'OR');
        }

    }

    public function realEstateStatistics($estate_type, $estate_reg_type)
    {
        $dataArray = array();
        foreach ($estate_type as $val) {
            foreach ($estate_reg_type as $value) {
                $where = array(
                    'estateType' => $val,
                    'regType' => $value,
                );
                $select = $this->getSql()->select();
                $select->columns(array('id' => new \Zend\Db\Sql\Expression('COUNT(*)')))
                    ->where($where);
                $result = $this->selectWith($select)->current();
                $dataArray[$val][$value] = $result->id;
            }
        }
        return $dataArray;
    }

    public function realEstateAgentStatistics($estate_type, $estate_reg_type, $userId)
    {

        $dataArray = array();
        foreach ($estate_type as $val) {
            foreach ($estate_reg_type as $value) {
                $where = array(
                    'estateType' => $val,
                    'regType' => $value,
                    'status' => array(1, 3, 4),
                );
                if ($userId)
                    $where['userId'] = array_keys($userId);
                $select = $this->getSql()->select();
                $select->columns(array('id' => new \Zend\Db\Sql\Expression('COUNT(*)'), 'userId'))
                    ->where($where);
                $select->group(array('userId'));
                $result = $this->selectWith($select);

                if ($result)
                    foreach ($result as $row) {
                        if (isset($userId[$row->userId]))
                            $dataArray[$userId[$row->userId]]['array'][$val][$value] = $row->id;
                        $dataArray[$userId[$row->userId]]['userId'] = $row->userId;
                    }
            }
        }
        return $dataArray;
    }

    public function systemSearch($keyword)
    {
        $fields_table = getSM()->get('fields_api')->init('real_estate');
        $data = '';
        if ($keyword) {
            $data = $this->getAll($fields_table, null, array(
                $this->table . '.status' => array(1, 3, 4),
                $this->table . '.expire > ?' => time(),
            ), $this->table . '.modified DESC', $keyword, 10);
        }
        return $data;
    }

//    public function getAreas()
//    {
//        if ($list = getCache()->getItem('realestate_estateArea'))
//            return $list;
//        $select = $this->getSql()->select();
//        $select->columns(array(new Db\Sql\Expression('DISTINCT(estateArea) as estateArea')))
//            ->order('estateArea ASC');
//        $data = $this->selectWith($select);
//        $list = array();
//        foreach ($data as $row) {
//            $list[] = $row->estateArea;
//        }
//
//        if ($list[0] != 0)
//            array_unshift($list, 0);
//
//        getCache()->setItem('realestate_estateArea', $list);
//        return $list;
//    }

    public function getPriceRange($areaId = null,$parentAreaId = null)
    {
        $cacheKey = self::SEARCH_CACHE_KEY;
        if ($parentAreaId)
            $cacheKey = 'region_statistic_' . $parentAreaId;
        if ($list = getCache()->getItem($cacheKey)) {
            if (isset($list['table']))
                return $list['table'];
        }
        $where = array(
            'status' => array(1, 3, 4),
            // 'expire > ?'=>time(),
        );
        $this->swapResultSetPrototype();
        $select = $this->getSql()->select();
        $select->columns(
            array(
                new Db\Sql\Expression('MAX(totalPrice) as max_totalPrice'),
                new Db\Sql\Expression('MIN(totalPrice) as min_totalPrice'),
                new Db\Sql\Expression('AVG(totalPrice) as avg_totalPrice'),
                new Db\Sql\Expression('MAX(mortgagePrice) as max_mortgagePrice'),
                new Db\Sql\Expression('MIN(mortgagePrice) as min_mortgagePrice'),
                new Db\Sql\Expression('AVG(mortgagePrice) as avg_mortgagePrice'),
                new Db\Sql\Expression('MAX(rentalPrice) as max_rentalPrice'),
                new Db\Sql\Expression('MIN(rentalPrice) as min_rentalPrice'),
                new Db\Sql\Expression('AVG(rentalPrice) as avg_rentalPrice'),
            ));

        if ($areaId)
            $where['areaId'] = $areaId;
        $select->where($where);
        $list['table'] = $this->selectWith($select)->current();
        $this->swapResultSetPrototype();
        getCache()->setItem($cacheKey, $list);
        return $list['table'];
    }

    /**
     * Gte the range(max , min) of dynamic fields values for spinner and slider elements
     *
     * @param string $table The dynamic fields data table name
     * @param array $fields a list of dynamic fields
     * @return array
     */
    public function getFieldsRangeValue($table, $fields)
    {
        if (!count($fields))
            return array();
        if ($list = getCache()->getItem(self::SEARCH_CACHE_KEY)) {
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

        getCache()->setItem(self::SEARCH_CACHE_KEY, $list);
        return $list['field'];
    }

    public function createLinkItems($parents, $pId, $type)
    {
        $items = array();
        $this->__sortItemForSiteMap($parents, $pId, $items, $type);
        return $items;
    }

    private function __sortItemForSiteMap(&$parents, $pId, &$items, $type)
    {
        if (isset($parents[$pId])) {
            foreach ($parents[$pId] as $item) {
                switch ($type) {
                    case 'estate_type':
                        $items[$item->id]['data'] = Common::Link($item->itemName, url('app/real-estate/list', array(), array('query' => array('table' => array('estateId' => $item->id)))));
                        break;
                    case 'estate_reg_type':
                        $items[$item->id]['data'] = Common::Link($item->itemName, url('app/real-estate/list', array(), array('query' => array('table' => array('regType' => $item->id)))));
                        break;
                }
                $this->__sortItemForSiteMap($parents, $item->id, $items[$item->id]['children'], $type);
            }
        }
    }

    /*public function getEstateTypeForSitemap()
    {
        $items = array();
        $select = $this->getSql()->select();
        getSM('translation_api')->translate($select, 'category_item');
        $select->where(array('catId' => 'estate_type'))
            ->order(array('parentId ASC'));
        $catItems = $this->selectWith($select);
        if ($catItems) {
            foreach ($catItems as $row)
                $items[] = Common::Link($row->itemName, url('app/real-estate/list', array(), array('query' => array('table' => array('estateId' => $row->id)))));
        }
        return $items;
    }*/

}


