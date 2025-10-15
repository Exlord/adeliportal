<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Fields\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class FieldTable extends BaseTableGateway
{
    protected $table = 'tbl_fields';
    protected $model = 'Fields\Model\Field';
    protected $caches = array('active_fields_list_with_type');
    protected $cache_prefix = array('active_fields_list_');
    protected $translateEntityType = 'fields';

    public $fieldTypes = array(
        'text' => 'Text Field',
        'long_text' => 'Long Text Field',
        'checkBox' => 'CheckBox',
        'radio' => 'Radio Button',
        'select' => 'Drop Down List Box',
        'uniqueCode' => 'Unique Code',
        'barcode' => 'Barcode',
        'currentDate' => 'Current Date',
        'fileUpload' => 'File Upload',
        'constant' => 'Static Text',
        'collection' => 'Collection'
    );

    public function getArray($entityType = 'all', $type = 0) //type=0 return fieldName , type=1 return all
    {
        $cache_key = 'active_fields_simple_list_' . $entityType;
        if (!$data = getCache()->getItem($cache_key)) {
            $where = array('status' => 1, 'collection' => 0,);
            if ($entityType != 'all')
                $where['entityType'] = $entityType;
            $items = $this->getAllTranslated($where, array('fieldOrder DESC'));

            $data = array();
            if ($type == 1)
                foreach ($items as $row) {
                    $data[$row->id] = (array)$row;
                }
            else
                foreach ($items as $row) {
                    $data[$row->id] = $row->fieldName;
                }
            getCache()->setItem($cache_key, $data);
        }
        return $data;
    }

    public function getByEntityType($entityType)
    {
        return $this->getAllTranslated(array('status' => 1, 'collection' => 0, 'entityType' => $entityType), array('fieldOrder DESC'));
    }

    public function getActiveFields($entityType)
    {
        $cache_key = 'active_fields_list_' . $entityType;
        if (!$data = getCache()->getItem($cache_key)) {
            $items = $this->getAllTranslated(array('status' => 1, 'collection' => 0, 'entityType' => $entityType), array('fieldOrder DESC'));
            $data = array();
            foreach ($items as $row) {
                $data[$row->id] = array(
                    'title' => $row->fieldName,
                    'mName' => $row->fieldMachineName,
                    'type' => $row->fieldType
                );
            }
            getCache()->setItem($cache_key, $data);
        }
        return $data;
    }

    public function getById($id)
    {
        if (!is_array($id)) {
            $id = array($id);
        }

        return $this->getAllTranslated(array('status' => 1, 'id' => $id), array('fieldOrder DESC'));
    }

    public function getCollectionFieldsArray($entityType)
    {
        $result = $this->select(array('collection' => 1, 'status' => 1, 'entityType' => $entityType));
        $fields = array();
        if ($result) {
            foreach ($result as $row) {
                $fields[$row->id] = $row->fieldName;
            }
        }

        return $fields;
    }
}