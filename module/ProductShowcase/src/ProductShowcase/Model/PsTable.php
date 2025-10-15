<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace ProductShowcase\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway;
use Zend\Paginator\Paginator;

class PsTable extends BaseTableGateway
{
    protected $table = 'tbl_product_showcase';
    protected $model = 'ProductShowcase\Model\Ps';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getDataArray($catId = null, $fields_table = null, $fields = null, $page = null, $itemCount = 20)
    {
        $whereCatId = '';
        $protoType = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $select = $this->getSql()->select();
        $tTable = getSM('translation_api')->translate($select, 'product_showcase');
        $sql = $this->getSql();

        $columns = array('psId' => 'id', 'status' => 'status', 'date' => 'date',
            'fPath' => new Db\Sql\Expression("(select fPath from tbl_file i where " . $this->table . '.id=i.entityId AND i.entityType="product_showcase" limit 1)')
        );

        //if translation is not gana be done add the over lapping fields
        if ($tTable === false)
            $columns['title'] = 'title';

        if ($catId)
            $whereCatId = ' AND cie.itemId=' . $catId;
        $ex_where = new Db\Sql\Expression($this->table . '.id=cie.entityId AND cie.entityType="product_showcase"' . $whereCatId);
//        $file_join = new Db\Sql\Expression($this->table . '.id=i.entityId AND i.entityType="product_showcase"');
        if ($catId)
            $select->join(array('cie' => 'tbl_category_item_entity'), $ex_where, array('itemId'));

        $select->columns($columns);
//            ->join(array('i' => 'tbl_file'), $file_join, array('fPath' => new Db\Sql\Expression('i.fPath'), 'fAlt' => 'fAlt', 'fTitle' => 'fTitle'), Select::JOIN_LEFT);
        if ($fields_table) {
            $fieldsColumns = array();
            if ($fields) {
                foreach ($fields as $f) {
                    $fieldsColumns[$f['fieldMachineName']] = $f['fieldMachineName'];
                }
            }

            $fieldsTranslation = getSM('translation_api')->translateFields('product_showcase_fields');
            if ($fieldsTranslation && count($fieldsTranslation)) {
                foreach ($fieldsTranslation[2] as $key => $value) {
                    if (isset($fieldsColumns[$key]))
                        unset($fieldsColumns[$key]);
                }
            }
            $select->join($fields_table, $this->table . '.id=' . $fields_table . '.entityId', $fieldsColumns, \Zend\Db\Sql\Select::JOIN_LEFT);
            if ($fieldsTranslation && count($fieldsTranslation))
                $select->join($fieldsTranslation[0], $fieldsTranslation[1], $fieldsTranslation[2], $fieldsTranslation[3]);
        }

        $select->where(array($this->table . '.status' => 1));
        $select->order($this->table . '.order DESC');
//        print($select->getSqlString($this->getAdapter()->getPlatform()));
//        die;
        if ($page != null)
            $result = $this->getPaginated($select, $sql, $page, $itemCount);
        else
            $result = $this->selectWith($select);
        $this->resultSetPrototype = $protoType;
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

    public function getById($id, $fields_table = null, $fields = null)
    {
        $whereCatId = '';
        $protoType = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $select = $this->getSql()->select();
        $tTable = getSM('translation_api')->translate($select, 'product_showcase');

        $columns = array('psId' => 'id', 'status' => 'status', 'date' => 'date');

        //if translation is not gana be done add the over lapping fields
        if ($tTable === false)
            $columns['title'] = 'title';

        $sql = $this->getSql();
        $ex_where = new Db\Sql\Expression($this->table . '.id=cie.entityId AND cie.entityType="product_showcase"');
        // $file_join = new Db\Sql\Expression($this->table . '.id=i.entityId AND i.entityType="product_showcase"');
        $select
            ->columns($columns)
            ->join(array('cie' => 'tbl_category_item_entity'), $ex_where, array('itemId'));
        //->join(array('i' => 'tbl_file'), $file_join, array('fPath' => new Db\Sql\Expression('GROUP_CONCAT(i.fPath)'), 'fAlt' => 'fAlt', 'fTitle' => 'fTitle'), Select::JOIN_LEFT);
        if ($fields_table) {

            $fieldsColumns = array();
            if ($fields) {
                foreach ($fields as $f) {
                    $fieldsColumns[$f['fieldMachineName']] = $f['fieldMachineName'];
                }
            }

            $fieldsTranslation = getSM('translation_api')->translateFields('product_showcase_fields');
            if ($fieldsTranslation && count($fieldsTranslation)) {
                foreach ($fieldsTranslation[2] as $key => $value) {
                    if (isset($fieldsColumns[$key]))
                        unset($fieldsColumns[$key]);
                }
            }
            $select->join($fields_table, $this->table . '.id=' . $fields_table . '.entityId', $fieldsColumns, \Zend\Db\Sql\Select::JOIN_LEFT);
            if ($fieldsTranslation && count($fieldsTranslation))
                $select->join($fieldsTranslation[0], $fieldsTranslation[1], $fieldsTranslation[2], $fieldsTranslation[3]);
        }
        $select->where(array($this->table . '.status' => 1, $this->table . '.id' => $id));
        $result = $this->selectWith($select)->current();
        $this->resultSetPrototype = $protoType;
        return $result;
    }

    public function getPsByIds($ids, $fields_table = null, $fields = null)
    {
        $protoType = $this->resultSetPrototype;
        $this->resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $select = $this->getSql()->select();
        $tTable = getSM('translation_api')->translate($select, 'product_showcase');
        $sql = $this->getSql();

        $columns = array('psId' => 'id', 'status' => 'status', 'date' => 'date',
            'fPath' => new Db\Sql\Expression("(select fPath from tbl_file i where " . $this->table . '.id=i.entityId AND i.entityType="product_showcase" limit 1)')
        );

        if ($tTable === false)
            $columns['title'] = 'title';

        $select->columns($columns);
        if ($fields_table) {
            $fieldsColumns = array();
            if ($fields) {
                foreach ($fields as $f) {
                    $fieldsColumns[$f['fieldMachineName']] = $f['fieldMachineName'];
                }
            }

            $fieldsTranslation = getSM('translation_api')->translateFields('product_showcase_fields');
            if ($fieldsTranslation && count($fieldsTranslation)) {
                foreach ($fieldsTranslation[2] as $key => $value) {
                    if (isset($fieldsColumns[$key]))
                        unset($fieldsColumns[$key]);
                }
            }
            $select->join($fields_table, $this->table . '.id=' . $fields_table . '.entityId', $fieldsColumns, \Zend\Db\Sql\Select::JOIN_LEFT);
            if ($fieldsTranslation && count($fieldsTranslation))
                $select->join($fieldsTranslation[0], $fieldsTranslation[1], $fieldsTranslation[2], $fieldsTranslation[3]);
        }

        $select->where(array($this->table . '.id' => $ids,$this->table . '.status' => 1));
        $select->order($this->table . '.order DESC');
//        print($select->getSqlString($this->getAdapter()->getPlatform()));
//        die;
        $result = $this->selectWith($select);
        $this->resultSetPrototype = $protoType;
        return $result;
    }
}
