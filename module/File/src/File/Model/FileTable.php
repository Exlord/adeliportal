<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace File\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class FileTable extends BaseTableGateway
{
    protected $table = 'tbl_file';
    protected $model = 'File\Model\File';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getByEntityType($entityType, $entityId, $filePathOnly = false)
    {
        $result = $this->select(array($this->table . '.entityType' => $entityType, $this->table . '.entityId' => $entityId));
        if (!$filePathOnly)
            return $result;
        $files = array();
        foreach ($result as $row) {
            $files[] = $row->fPath;
        }
        return $files;
    }

    public function removeByFileName($fileName)
    {
        try {
            return $this->delete(array('fPath' => $fileName));
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function removeById($id)
    {
        try {
            return $this->delete(array('entityId' => $id));
        } catch (Exception $e) {
            return $e;
        }
    }

    public function removeByEntityType($entityType,$id)
    {
        $imagesFile = $this->getByEntityType($entityType, $id, true);
        foreach ($imagesFile as $val)
            unlink(PUBLIC_PATH . $val);
        $this->delete(array('entityId'=>$id));
    }

}
