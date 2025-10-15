<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Mail\Model;


use System\DB\BaseTableGateway;

class MailArchiveTable extends BaseTableGateway
{
    protected $table = 'tbl_mail_archive';
    protected $model = 'Mail\Model\Mail';
    protected $caches = null;
    protected $cache_prefix = null;

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct($this->table, $adapter);
    }
}
