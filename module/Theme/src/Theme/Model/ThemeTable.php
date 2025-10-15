<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Theme\Model;

use Application\API\App;
use System\DB\BaseTableGateway;
use Theme\API\Themes;
use Zend\Db;
use Zend\Db\TableGateway;

class ThemeTable extends BaseTableGateway
{
    protected $table = 'tbl_themes';
    protected $model = 'Theme\Model\Theme';
    protected $caches = null;
    protected $cache_prefix = null;

    private $_defaultThemes = null;

    public function getByName($name)
    {
        $result = $this->select(array('name' => $name));
        if ($result)
            return $result->current();
        else
            return null;
    }

    public function removeDefault($type)
    {
        $this->update(array('default' => 0), array('type' => $type));
    }

    public function setDefault($name)
    {
        $all_themes = getSM('theme_api')->getTemplates();
        $template = $this->getByName($name);
        if ($template)
            $this->update(array('default' => 1), array('name' => $name));
        else
            $this->insert(array(
                'name' => $name,
                'type' => Themes::$types[$all_themes[$name]['type']],
                'default' => 1
            ));
        $this->_defaultThemes = null;
        getCache(true)->removeItem('default_themes');
    }

    public function getDefaults()
    {
        if ($this->_defaultThemes == null) {
            $cacheKey = 'default_themes';
            $this->_defaultThemes = getCache(true)->getItem($cacheKey);
            if (!$this->_defaultThemes){
                $themes = array();
                $themesList = $this->getAll(array('default' => 1));
                foreach ($themesList as $row) {
                    $themes[$row->type] = $row;
                }
                getCache(true)->setItem($cacheKey, $themes);
                $this->_defaultThemes = $themes;
            }
        }
        return $this->_defaultThemes;
    }
}
