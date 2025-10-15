<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 1:19 PM
 */
namespace System\DB;


use System\Model\BaseModel;
use System\Paginator\Adapter\DbSelect;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature\FeatureSet;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class BaseTableGateway
    extends AbstractTableGateway
    implements ServiceLocatorAwareInterface
{
    protected $model = null;
    protected $caches = null;
    protected $cache_prefix = null;
    protected $primaryKey = 'id';
    protected $_oldResultSetPrototype = null;
    protected $translateEntityType = null;

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    protected function _clearCache()
    {
        if (is_array($this->caches)) {
            getCache()->removeItems($this->caches);
        }
        if (is_array($this->cache_prefix)) {
            foreach ($this->cache_prefix as $pre)
                getCache()->clearByPrefix($pre);
        }
    }

    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->services;
    }

    public function __construct($table = null, $adapter = null)
    {
//        if ($this->model == null)
//            throw new \Exception('$model property is required. Please provide a model class name as string. ex : \namespace\class');

        if ($table)
            $this->table = $table;
        if ($this->table == null)
            throw new \Exception('Database Table name has not been specified. $table');

        if ($this->model != null) {
            $model = $this->model;
            $resultSet = new HydratingResultSet(
                new ClassMethods(false),
                new $model());
            $this->resultSetPrototype = $resultSet;
        }

        if (!$adapter) {
            $this->featureSet = new FeatureSet();
            $this->featureSet->addFeature(new GlobalAdapterFeature());
        } else
            $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * @param $id
     * @param array $columns
     * @return array|\ArrayObject|null|\Zend\Db\ResultSet\ResultSet
     */
    public function get($id, $columns = false)
    {
        if (!is_array($id))
            $id = array($id);

        array_walk($id, function (&$item, $index) {
            $item = (int)$item;
        });

        $where = array($this->primaryKey => $id);
        if ($columns) {
            $select = $this->getSql()->select()->columns($columns)->where($where);
            $rowset = $this->selectWith($select);
        } else {
            $rowset = $this->select($where);
        }
        if (!$rowset)
            return null;

        if (count($id) == 1)
            return $rowset->current();

        return $rowset;
    }

    /**
     * @param $model Object|Array|BaseModel
     * @return int
     * @throws \Exception
     */
    public function save($model)
    {
        $this->_clearCache();

        if (is_object($model)) {
            $hydrator = new ClassMethods(false);
            $model_data = $hydrator->extract($model);
        } else
            $model_data = $model;

        if (is_object($model) && $model instanceof BaseModel) {
            $filter = $model->filters;
            if (is_array($filter) && count($filter)) {
                foreach ($filter as $f) {
                    if (array_key_exists($f, $model_data))
                        unset($model_data[$f]);
                }
            } elseif (is_string($filter)) {
                if (isset($model_data[$filter]))
                    unset($model_data[$filter]);
            }
            unset($model_data['filter']);
        }

        $id = $this->primaryKey ? (int)@$model_data[$this->primaryKey] : 0;
        if ($id == 0) {
            $result = $this->insert($model_data);
            if (is_object($model) && $this->primaryKey)
                $model->{$this->primaryKey} = $this->lastInsertValue;
            return $this->lastInsertValue;
        } else {
            return $this->update($model_data, array($this->primaryKey => $id));
        }
    }

    public function multiSave(array $models)
    {
        $updates = array();
        $inserts = array();
        $models_data = array();

        //convert objects to arrays
        foreach ($models as $model) {
            if (is_object($model)) {
                $hydrator = new ClassMethods(false);
                $model_data = $hydrator->extract($model);
            } else
                $model_data = $model;
            $models_data[] = $model_data;
        }

        //seprate inserts from updates
        foreach ($models_data as $model) {
            $id = (int)(@$model['id']);
            if ($id === 0) {
                unset($model['id']);
                $inserts[] = $model;
            } else
                $updates[] = $model;
        }

        $effectedRows = 0;
        $this->_clearCache();
        $this->getAdapter()->getDriver()->getConnection()->beginTransaction();
        $insertIds = array();
        if (count($inserts)) {
            foreach ($inserts as $values) {
                if ($this->insert($values)) {
                    $effectedRows++;
                    $insertIds[] = (int)$this->getLastInsertValue();
                }
            }
        }
        if (count($updates)) {
            foreach ($updates as $values) {
                $effectedRows += $this->update($values, array($this->primaryKey => $values[$this->primaryKey]));
            }
        }
        $this->getAdapter()->getDriver()->getConnection()->commit();
        return array('effectedRows' => $effectedRows, 'inserts' => $insertIds);
    }

//    public function multiInsert(array $inserts)
//    {
//        $this->_clearCache();
//        $data = array();
//        foreach ($inserts as $model) {
//            unset($model[$this->primaryKey]);
//            foreach ($model as $field) {
//                $data[] = $field;
//            }
//        }
//
//        $columns = array_keys(current($inserts));
//        $platform = $this->adapter->platform;
//        array_walk($columns, function (&$item, $index) use ($platform) {
//            $item = $platform->quoteIdentifier($item);
//        });
//
//        $columnPlaceholder = "(" . implode(',', array_fill(0, count($columns), '?')) . ")";
//        $valuesPlaceholder = implode(',', array_fill(0, count($data), $columnPlaceholder));
//        $query = "INSERT INTO " .
//            $this->adapter->platform->quoteIdentifier($this->table) .
//            " (" . implode(',', $columns) . ") VALUES " . $valuesPlaceholder;
//
//        $this->getAdapter()->query($query, $data);
//    }

    /**
     * Update
     *
     * @param  array $set
     * @param  string|array|closure $where
     * @return int
     */
    public function update($set, $where = null)
    {
        $this->_clearCache();
        return parent::update($set, $where);
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $this->_clearCache();
        $this->delete(array($this->primaryKey => $id));
    }

    public function getAll($where = null, $order = null, $limit = null, $pageNumber = false)
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
            $itemCount = 20;
            if ($limit)
                $itemCount = $limit;

            return $this->getPaginated($select, $sql, $pageNumber, $itemCount);
        } else {
            return $this->selectWith($select);
        }
    }

    public function getAllTranslated($where = null, $order = null, $limit = null)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        if ($this->translateEntityType)
            getSM('translation_api')->translate($select, $this->translateEntityType);
        if ($where)
            $select->where($where);
        if ($order)
            $select->order($order);
        if ($limit)
            $select->limit($limit);
        return $this->selectWith($select);
    }

    public function getPaginated($select, $sql, $page, $itemCount = 20)
    {
        $adapter = new DbSelect($select, $sql);
        $pagination = new Paginator($adapter);
        $pagination->setCurrentPageNumber($page);
        $pagination->setItemCountPerPage($itemCount);
        return $pagination;
    }

    /**
     * @param $id
     * @param $field string The name of the field
     * @param int $addition how much to add
     */
    public function updateCounter($id, $field, $addition = 1)
    {
        $this->update(array($field => new Expression($field . '+' . $addition)), array($this->primaryKey => $id));
    }

    protected function toNestedArray($list, $parentColumn = 'parentId', $idColumn = 'id')
    {
        $items = array();
        $parents = array();
        foreach ($list as $item) {
            $parents[$item->{$parentColumn}][] = $item;
        }
        $this->recursiveArray($items, $parents, 0, -1, $idColumn);
        return $items;
    }

    private function recursiveArray(&$items, $parents, $parentId, $indent, $idColumn)
    {
        ++$indent;
        if (is_array($parents) && isset($parents[$parentId])) {
            foreach ($parents[$parentId] as $item) {
                $item->indent = $indent;
                $items[$item->{$idColumn}] = $item;
                if (isset($parents[$item->{$idColumn}])) {
                    $this->recursiveArray($items, $parents, $item->{$idColumn}, $indent, $idColumn);
                }
            }
        }
    }

    /**
     * Remove an item and all its childes
     */
    protected function getChildren($id, $includeSelf = true, $parentColumn = 'parentId', $idColumn = 'id')
    {
        if (!is_array($id))
            $id = array($id);

        $all = $this->getAll();
        $items = array();
        $parents = array();
        $self = null;
        foreach ($all as $item) {
            if ((is_array($id) && in_array($item->{$idColumn}, $id)))
                $self = $item;
            $parents[$item->{$parentColumn}][] = $item;
        }

        foreach ($id as $i)
            $this->getChildesRecursive($items, $parents, $i, $idColumn);

        if ($includeSelf)
            $items[$self->{$idColumn}] = $self;

        return $items;
    }

    /**
     * @param array $items An empty array that will be filled with id's to be deleted
     * @param $parents
     * @param $parentId
     * @param $idColumn
     */
    private function getChildesRecursive(&$items, $parents, $parentId, $idColumn)
    {
        if (isset($parents[$parentId])) {
            foreach ($parents[$parentId] as $item) {
                $items[$item->{$idColumn}] = $item;
                if (isset($parents[$item->{$idColumn}])) {
                    $this->getChildesRecursive($items, $parents, $item->{$idColumn}, $idColumn);
                }
            }
        }
    }

    /**
     * Converts an Object's properties to array
     * @param $model
     * @return array
     */
    public function toArray($model)
    {
        if (is_object($model)) {
            $hydrator = new ClassMethods(false);
            $model = $hydrator->extract($model);
        }
        return $model;
    }

    /**
     * @param \Zend\Db\Sql\Select $select
     * @return HydratingResultSet
     * @throws \RuntimeException
     */
    public function selectWith(Select $select)
    {
        return parent::selectWith($select);
    }

    public function swapResultSetPrototype($objectPrototype = null)
    {
        if ($this->_oldResultSetPrototype == null) {
            $this->_oldResultSetPrototype = $this->resultSetPrototype;
            $this->resultSetPrototype = new HydratingResultSet(null, $objectPrototype);
        } else {
            $this->resultSetPrototype = $this->_oldResultSetPrototype;
            $this->_oldResultSetPrototype = null;
        }
    }

    /**
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getAdapter()
    {
        return parent::getAdapter();
    }

    /**
     * return a List Of This Tables columns
     * @return array
     */
    public function getFields()
    {
        if ($this->model != null) {
            $model = $this->model;
            $model = new $model();
            $model_array = $this->toArray($model);
            if (is_object($model) && $model instanceof BaseModel) {
                $filter = $model->filters;
                if (is_array($filter) && count($filter)) {
                    foreach ($filter as $f) {
                        if (array_key_exists($f, $model_array))
                            unset($model_array[$f]);
                    }
                } elseif (is_string($filter)) {
                    if (isset($model_array[$filter]))
                        unset($model_array[$filter]);
                }
                unset($model_array['filter']);
            }
            return array_keys($model_array);
        }
        return array();
    }

    public function multipleSave($array)
    {
        try {
            foreach ($array as $val) {
                $this->save($val);
            }
            return 1;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function makeIndentedArray($listItems, $valueFieldName, $indentFieldName = 'indent')
    {
        $arrayItems = array();
        foreach ($listItems as $key => $item) {
            $indent = $item->{$indentFieldName};
            $arrayItems[$key] = str_repeat('--', (int)$indent) . ($indent ? '|- ' : '') . $item->{$valueFieldName};
        }
        return $arrayItems;
    }

    public function qi($value)
    {
        return $this->getAdapter()->getPlatform()->quoteIdentifier($value);
    }

    public function qv($value)
    {
        return $this->getAdapter()->getPlatform()->quoteValue($value);
    }
}
