<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace FormsManager\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class FormDataTable extends BaseTableGateway
{
    protected $table = 'tbl_forms_data';
    protected $model = 'FormsManager\Model\FormData';
    protected $caches = null;
    protected $cache_prefix = null;

    /**
     * Get data for only 1 form
     * @param int $id
     * @param array|bool $formId
     * @param $fieldIds
     * @param $forView
     * @return array
     */
    public function getData($id, $formId, $fieldIds, $forView = false, $editable = true)
    {
        $id = (int)$id;
        getSM('fields_api')->init('form_' . $formId);
        $fieldsData = getSM('fields_api')->getFieldData($id, $fieldIds, $forView, $editable);
        $formData = $this->toArray(parent::get($id));

        $formData['formDataId'] = $formData['id'];
        unset($formData['id']);
        if (is_array($fieldsData))
            $formData = array_merge($formData, $fieldsData);
        if (!$editable) {
            unset($formData['formDataId']);
            unset($formData['id']);
        }
        return $formData;
    }
}
