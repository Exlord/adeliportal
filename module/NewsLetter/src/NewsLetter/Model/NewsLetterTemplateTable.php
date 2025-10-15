<?php
namespace NewsLetter\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class NewsLetterTemplateTable extends BaseTableGateway
{
    protected $table = 'tbl_news_letter_template';
    protected $model = 'NewsLetter\Model\NewsLetterTemplate';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getNewsTemplate()
    {
        return $this->getAll()->toArray();
    }

    public function getArrayTheme()
    {
        $dataArray = array();
        $select = $this->getAll();
        if ($select->count())
            foreach ($select as $row)
                $dataArray[$row->id] = $row->desc;
        return $dataArray;
    }


}


