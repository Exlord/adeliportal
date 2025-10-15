<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/8/2014
 * Time: 1:48 PM
 */

namespace File\Model;


use System\DB\BaseTableGateway;

class PFileTable extends BaseTableGateway
{
    protected $table = 'tbl_file_private';
    protected $model = 'File\Model\PFile';

    public function get($id)
    {
        $model = parent::get($id);
        $model->accessibility = @unserialize($model->accessibility);
        return $model;
    }

    public function save($model)
    {
        $model->accessibility = serialize($model->accessibility);
        return parent::save($model);
    }

    public function getArray()
    {
        $select = $this->getSql()->select();
        $select->columns(array('id', 'name', 'title'));
        $select->order('title ASC');
        $data = $this->selectWith($select);
        $files = array();
        if ($data) {
            foreach ($data as $row) {
                $files[$row->id] = $row->name . ' - ' . $row->title;
            }
        }
        return $files;
    }
}