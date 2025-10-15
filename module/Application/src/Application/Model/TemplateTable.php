<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Application\Model;


use System\DB\BaseTableGateway;

class TemplateTable extends BaseTableGateway
{
    protected $table = 'tbl_template';
    protected $model = 'Application\Model\Template';
    protected $caches = array('application_templates');
    protected $cache_prefix = null;

    public function getArray()
    {
        $cacheKey = 'application_templates';
        if (!$list = getCacheItem($cacheKey)) {
            $data = $this->select();
            $list = array();
            foreach ($data as $row) {
                $list[$row->id] = $row->title;
            }
            setCacheItem($cacheKey, $list);
        }
        return $list;
    }

}
