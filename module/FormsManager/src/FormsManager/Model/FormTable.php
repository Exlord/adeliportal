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

class FormTable extends BaseTableGateway
{
    protected $table = 'tbl_forms';
    protected $model = 'FormsManager\Model\Form';
    protected $caches = null;
    protected $cache_prefix = array('forms_data_rendered_template_', 'form_fields_list_', 'form_field_names_');

    public function getById($id)
    {
        $select = $this->getSql()->select();
        getSM('translation_api')->translate($select, 'dynamic_form');
        $select->where(array('id' => $id));
        $result = $this->selectWith($select);
        if ($result && $result->count())
            return $result->current();

        return null;
    }
}
