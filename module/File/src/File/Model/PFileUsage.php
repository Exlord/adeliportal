<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/12/2014
 * Time: 11:47 AM
 */

namespace File\Model;


use System\DB\BaseTableGateway;

class PFileUsage extends BaseTableGateway
{
    protected $table = 'tbl_file_private_usage';

    public function removeByEntity($entityType, $entityId)
    {
        $this->delete(array('entityType' => $entityType, 'entityId' => $entityId));
    }

    public function removeByFileId($fileId)
    {
        $this->delete(array('fileId' => $fileId));
    }

    public function saveAll($entityType, $entityId, $files)
    {
        $this->removeByEntity($entityType, $entityId);
        $items = array();
        foreach ($files as $val) {
            $item = array(
                'entityId' => $entityId,
                'entityType' => $entityType,
                'fileId' => $val,
            );
            $items[] = $item;
        }
        $this->multiSave($items);
    }

    public function getFiles($entityType, $entityId)
    {
        $data = $this->select(array('entityType' => $entityType, 'entityId' => $entityId));
        $files = array();
        if ($data) {
            foreach ($data as $row) {
                $files[] = $row['fileId'];
            }
        }
        return $files;
    }

    public function getFilesData($entityType, $entityId)
    {
        $select = $this->getSql()->select();
        $select
            ->columns(array())
            ->join(array('f' => 'tbl_file_private'), $this->table . '.fileId=f.id', array('id', 'name', 'title'))
            ->where(array($this->table . '.entityType' => $entityType, $this->table . '.entityId' => $entityId));
        $result = $this->selectWith($select);
        if ($result) {
            return $result;
        }
        return null;
    }
} 