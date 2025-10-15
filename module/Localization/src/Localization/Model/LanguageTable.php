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

class LanguageTable extends BaseTableGateway
{
    protected $table = 'tbl_languages';
    protected $model = 'Localization\Model\Language';
    protected $caches = null;
    protected $cache_prefix = array('active_languages_array_');

    private $defaultLang;

    public function getAllActive()
    {
        return $this->getAll(array('status' => 1));
    }

    public function getArray($default = false)
    {
        $cacheKey = 'active_languages_array_';
        if ($default === false)
            $cacheKey .= 'no_default';
        else
            $cacheKey .= 'with_default';

        if (!$languages = getCacheItem($cacheKey, true)) {
            $where = array('status' => 1);
            $order = array();

            if ($default === false)
                $where['default'] = '0';
            else
                $order = array('default DESC');

            $langs = $this->getAll($where, $order);
            $languages = array();
            if ($langs && $langs->count()) {
                foreach ($langs as $l) {
                    $languages[$l->langSign] = $l->langName;
                }
            }
            setCacheItem($cacheKey, $languages, true);
        }
        return $languages;
    }

    //kept for backward compatibility remove later
    public function getDefaultLang()
    {
        return $this->getDefault();
    }

    public function getDefault($fresh = false)
    {
        if ($this->defaultLang && !$fresh)
            $l = $this->defaultLang;
        else {
            $cacheKey = 'default_lang';

            $l = getCache(true)->getItem($cacheKey);
            if ($fresh || !$l) {
                $select = $this->sql->select();
                $select
                    ->where(array('default' => 1))
                    ->limit(1);
                $result = $this->selectWith($select);
                if ($result && $result->count())
                    $l = $result->current()->langSign;
                else
                    $l = 'fa';

                getCache(true)->setItem($cacheKey, $l);
            }
            $this->defaultLang = $l;
        }

        return $l;
    }

    public function setDefaultByLang($lang)
    {
        $this->update(array('default' => 1), array('langSign' => $lang));
        getCache(true)->removeItem('default_lang');
    }

    public function setDefaultById($id)
    {
        $this->update(array('default' => 1), array('id' => $id));
        getCache(true)->removeItem('default_lang');
    }
}
