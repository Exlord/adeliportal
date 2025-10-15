<?php

namespace NewsLetter\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class NewsletterSignUpTable extends BaseTableGateway
{
    protected $table = 'tbl_news_letter_sign_up';
    protected $model = 'NewsLetter\Model\NewsletterSignUp';
    protected $cache_prefix = null;

    public function getEmails($type = 0) //type = 0 get object & type=1 get array
    {
        $result = $this->getAll(array('status' => 1));
        if ($result->count()) {
            if ($type) {
                $dataArray = false;
                foreach ($result as $row)
                    $dataArray[$row->email] = unserialize($row->config);
                return $dataArray;
            } else
                return $result;
        }
    }
}
