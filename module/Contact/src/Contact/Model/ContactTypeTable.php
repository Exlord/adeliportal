<?php

namespace Contact\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class ContactTypeTable extends BaseTableGateway
{
    protected $table = 'tbl_contact_type';
    protected $model = 'Contact\Model\ContactType';
    protected $caches = '';
    protected $cache_prefix = array('contact_type_array_');

    public function removeAllTypeByContactUser($contactUserId)
    {
        $types = $this->getAll(array('contactUserId' => $contactUserId));
        if ($types->count() > 0) {
            $arrayId = array();
            foreach ($types as $row)
                $arrayId[] = $row->id;
            $this->delete(array('id' => $arrayId));
        }
    }

    public function getArray($contactUserId, $type = 0)
    {
        $cacheKey = 'contact_type_array_' . $contactUserId;
        if ($dataArray = getCache($cacheKey)) {
            $select = $this->getSql()->select();
            getSM('translation_api')->translate($select, 'contact_type');
            $select->where(array('contactUserId' => $contactUserId));
            $types = $this->selectWith($select);

            $dataArray = null;
            if ($types) {
                if ($type)
                    foreach ($types as $row)
                        $dataArray[$row->id] = $row->title;
                else
                    foreach ($types as $row)
                        $dataArray[] = array('selectName' => $row->title);
            }
            setCacheItem($cacheKey, $dataArray);
        }
        return $dataArray;
    }
}
