<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Localization\Model;

use Application\API\App;
use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class LanguageContentTable extends BaseTableGateway
{
    protected $table = 'tbl_language_content';
    protected $caches = null;
    protected $cache_prefix = null;

    public function remove($langSign)
    {
        $this->delete(array('langSign' => $langSign));
    }

    public function removeByEntity($entityId, $entityType)
    {
        $this->delete(array('entityId' => $entityId, 'entityType' => $entityType));
    }

    public function add(array $langs, $entityId, $entityType)
    {
        if (count($langs)) {
            foreach ($langs as $d) {
                if (!empty($d)) {
                    $this->insert(array(
                        'langSign' => $d,
                        'entityId' => $entityId,
                        'entityType' => $entityType
                    ));
                }
            }
        }
    }

    public function getLangs($entityId, $entityType)
    {
        $select = $this->getSql()->select();
        $select
            ->columns(array('langSign'))
            ->where(array('entityId' => $entityId, 'entityType' => $entityType));
        $langs = $this->selectWith($select);
        $langsArray = array();
        foreach ($langs as $d) {
            $langsArray[] = $d->langSign;
        }
        return $langsArray;
    }
}
