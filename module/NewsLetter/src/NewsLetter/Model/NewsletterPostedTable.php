<?php

namespace NewsLetter\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class NewsletterPostedTable extends BaseTableGateway
{
    protected $table = 'tbl_newsletter_posted';
    protected $model = 'NewsLetter\Model\NewsletterPosted';
    protected $cache_prefix = null;
    protected $primaryKey = null;
}
