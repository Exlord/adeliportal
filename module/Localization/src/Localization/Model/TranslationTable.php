<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/5/12
 * Time: 10:25 AM
 */
namespace Localization\Model;

use Application\Model\Config;
use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class TranslationTable extends TableGateway\TableGateway
{
    public function getAll($entityId, $lang)
    {
        $where = array('entityId' => $entityId);
        if ($lang != 'all')
            $where['lang'] = $lang;
        return $this->select($where);
    }

    public function multiSave($entityId, $data)
    {
        if (count($data)) {
            $updates = $inserts = null;
            if (isset($data['edit']))
                $updates = $data['edit'];
            if (isset($data['new']))
                $inserts = $data['new'];

            $this->getAdapter()->getDriver()->getConnection()->beginTransaction();
            if ($inserts && count($inserts)) {
                foreach ($inserts as $lang => $values) {
                    $values['lang'] = $lang;
                    $values['entityId'] = $entityId;
                    $this->insert($values);
                }
            }
            if ($updates && count($updates)) {
                foreach ($updates as $lang => $values) {
                    $this->update($values, array('entityId' => $entityId, 'lang' => $lang));
                }
            }
            $this->getAdapter()->getDriver()->getConnection()->commit();
        }
    }
}
